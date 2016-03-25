<?php
/**
 * This file is part of the Global Trading Technologies Ltd crypt-bundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gtt\Bundle\CryptBundle\Bridge\Aes;

use Gtt\Bundle\CryptBundle\Encryption\EncryptorInterface;
use Gtt\Bundle\CryptBundle\Exception\SymmetricEncryptionException;
use Crypto;
use CryptoTestFailedException;
use CannotPerformOperationException;

/**
 * Perform symmetric encryption of message
 */
class AesEncryptor implements EncryptorInterface
{
    /**
     * Key reader
     *
     * @var KeyReader
     */
    private $keyReader;

    /**
     * Ciphertext should be Base64-encoded
     *
     * @var bool
     */
    private $binaryOutput;

    /**
     * Constructor
     *
     * @param KeyReader $keyReader    Key reader
     * @param bool      $binaryOutput Ciphertext should be raw binary
     */
    public function __construct(KeyReader $keyReader, $binaryOutput)
    {
        $this->keyReader    = $keyReader;
        $this->binaryOutput = $binaryOutput;
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($value)
    {
        try {
            $ciphertext = Crypto::Encrypt($value, $this->keyReader->read());
            if (!$this->binaryOutput) {
                $ciphertext = base64_encode($ciphertext);
                if ($ciphertext === false) {
                    throw new SymmetricEncryptionException('Cannot encode message to base64');
                }
            }
            return $ciphertext;
        } catch (CryptoTestFailedException $e) {
            throw SymmetricEncryptionException::cryptoTestFailed($e);
        } catch (CannotPerformOperationException $e) {
            throw SymmetricEncryptionException::cannotPerformOperation($e);
        }
    }
}