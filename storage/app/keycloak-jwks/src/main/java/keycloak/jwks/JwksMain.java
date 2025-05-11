/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package keycloak.jwks;

import org.apache.commons.io.FilenameUtils;
import com.fasterxml.jackson.annotation.JsonInclude.Include;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.databind.ObjectWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.FileInputStream;
import java.io.InputStream;
import java.security.KeyStore;
import org.keycloak.jose.jwk.JSONWebKeySet;
import org.keycloak.jose.jwk.JWK;
import org.keycloak.jose.jwk.JWKBuilder;
import java.util.Base64;
import java.util.regex.Pattern;
import java.util.regex.Matcher;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.File;
//import java.nio.file.Path;
//import java.nio.file.Paths;

/**
 *
 * @author rmartinc
 */
public class JwksMain {

    private final String keystore;
    private final String password;
    private final String alias;
    private final String algorithm;

    public JwksMain(String... args) {
        if (args.length != 3 && args.length != 4) {
            usage("Invalid arguments");
        }

        keystore = args[0];
        alias = args[1];
        algorithm = args[2];

        if (args.length == 4) {
            password = args[3];
        }
        else {
            password = "";
        }
    }

    private void usage(String error) {
        String nl = System.getProperty("line.separator");

        throw new IllegalArgumentException(new StringBuilder()
                .append(error != null? "ERROR: " + error : "").append(nl)
                .append("mvn exec:java@jwks -Dexec.args=\"<keystore.jks> (*.p12|*.jks) <alias> <algorithm> <keystore-password>\"").append(nl)
                .append("Example:").append(nl)
                .append("mvn exec:java@jwks -Dexec.args=\"keystore-rsa.jks password sample RS256\"").append(nl)
                .toString());
    }

    private void execute() throws Exception {
        //Path currentRelativePath = Paths.get("");
        //String s = currentRelativePath.toAbsolutePath().toString();
        //System.out.println("Current absolute path is: " + s);

        File file = new File(keystore);

        if (!file.exists()) {
            usage("Invalid file: " + keystore);
        }
        
        String keystoreType = FilenameUtils.getExtension(keystore);
        KeyStore store = null;
        
        switch (keystoreType.toUpperCase()) {
            case "P12":
                store = KeyStore.getInstance("PKCS12");
                break;
            case "JKS":
                store = KeyStore.getInstance("JKS");
                break;
            default:
                usage("keystoreType key type: " + keystoreType.toUpperCase() + " supported PKCS12 or JKS");
                break;
        }
        
        try (InputStream is = new FileInputStream(file)) {
            store.load(is, password.toCharArray());
        }
        
        KeyStore.Entry entry = store.getEntry(alias, new KeyStore.PasswordProtection(password.toCharArray()));
        
        if (!(entry instanceof KeyStore.PrivateKeyEntry)) {
            usage("Invalid private key entry: " + alias);
        }

        KeyStore.PrivateKeyEntry key = (KeyStore.PrivateKeyEntry) entry;
        JSONWebKeySet keySet = new JSONWebKeySet();
        JWK jwk = null;

        writePemFile(Base64.getEncoder().encodeToString(key.getCertificate().getPublicKey().getEncoded()), "../client.rs256.pubkey.pem", "-----BEGIN PUBLIC KEY-----", "-----END PUBLIC KEY-----");
        writePemFile(Base64.getEncoder().encodeToString(key.getPrivateKey().getEncoded()), "../client.rs256.pvtkey.pem", "-----BEGIN PRIVATE KEY-----", "-----END PRIVATE KEY-----");
        writePemFile(Base64.getEncoder().encodeToString(key.getCertificate().getEncoded()), "../client.rs256.cert.pem", "-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----");

        switch (key.getPrivateKey().getAlgorithm()) {
            case "RSA":
                jwk = JWKBuilder.create().algorithm(algorithm).rsa(key.getCertificate().getPublicKey());
                break;
            case "EC":
                jwk = JWKBuilder.create().algorithm(algorithm).ec(key.getCertificate().getPublicKey());
                break;
            default:
                usage("Invalid key type: " + key.getPrivateKey().getAlgorithm());
                break;
        }

        keySet.setKeys(new JWK[]{jwk});

        ObjectWriter ow = new ObjectMapper().setSerializationInclusion(Include.NON_NULL).writer().withDefaultPrettyPrinter();

        writeJsonFile(ow.writeValueAsString(keySet), "../jwks.json");
        
        return;
    }

    public void writePemFile(String text, String fileName, String header, String footer) throws IOException {
        File fileObj = new File(fileName); 
        
        if(fileObj.exists() && !fileObj.isDirectory()) { 
            fileObj.delete();
        }

        BufferedWriter out = new BufferedWriter(new FileWriter(fileName, true));

        Pattern pattern = Pattern.compile(".{1,64}");
        Matcher matcher = pattern.matcher(text);

        out.write(header + "\n");
 
        while (matcher.find()) {
            String part = text.substring(matcher.start(), matcher.end());
            
            out.write(part + "\n");
        }

        out.write(footer);

        out.close();

        return;
    }

    public void writeJsonFile(String text, String fileName) throws IOException {
        File fileObj = new File(fileName); 

        if(fileObj.exists() && !fileObj.isDirectory()) { 
            fileObj.delete();
        }

        BufferedWriter out = new BufferedWriter(new FileWriter(fileName));

        out.write(text);

        out.close();

        return;
    }

    public static void main(String... args) throws Exception {
        JwksMain main = new JwksMain(args);
        main.execute();

        return;
    }
}
