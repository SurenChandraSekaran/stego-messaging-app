<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;

class ChatSecurityService
{
    /**
     * Generate a unique 256-bit key for a conversation.
     */
    public function generateKey()
    {
        // This generates a high-entropy random key
        return base64_encode(random_bytes(32));
    }

    /**
     * Get an Encrypter instance for a specific conversation.
     */
    private function getEncrypter($key)
    {
        return new Encrypter(base64_decode($key), 'AES-256-CBC');
    }

    /**
     * Encrypt data using the conversation's unique key.
     */
    public function encrypt($value, $key)
    {
        if (!$key) return $value;
        return $this->getEncrypter($key)->encrypt($value);
    }

    /**
     * Decrypt data using the conversation's unique key.
     */
    public function decrypt($value, $key)
    {
        if (!$key) return $value;
        try {
            return $this->getEncrypter($key)->decrypt($value);
        } catch (\Exception $e) {
            return "[Decryption Error]";
        }
    }
}