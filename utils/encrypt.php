<?php

/*
    Koda apraksts:
        Utilitfails, kurs satur vienkarsas sifresanas klasi.
        Klase satur funkcijas, kuras sifre vai atsifre ievadito
        parametru.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

class EncryptorBasic{
    public function encrypt($string){
        // Store the cipher method
        $ciphering = "AES-128-CTR";
        
        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        
        // Non-NULL Initialization Vector for encryption
        $encryption_iv = '1234567891011121';
        
        // Store the encryption key
        $encryption_key = "SchedulerSweetCakes";

        $encryption = openssl_encrypt($string, $ciphering,
                    $encryption_key, $options, $encryption_iv);
        
        return $encryption;
    }

    public function decrypt($string){
        
        // Store the cipher method
        $ciphering = "AES-128-CTR";

        $decryption_iv = '1234567891011121';
        $options = 0;
  
        // Store the decryption key
        $decryption_key = "SchedulerSweetCakes";
        
        // Use openssl_decrypt() function to decrypt the data
        $decryption=openssl_decrypt ($string, $ciphering, 
                $decryption_key, $options, $decryption_iv);

        return $decryption;
    }
}