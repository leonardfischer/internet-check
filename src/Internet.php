<?php

namespace lfischer\internet;

use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class Checker
 * @package lfischer\internet
 * @see http://stackoverflow.com/questions/4860365/determine-in-php-script-if-connected-to-internet
 */
class Internet
{
    /**
     * @var string
     */
    private $hostname = 'www.google.com';

    /**
     * @var int
     */
    private $port = 80;

    /**
     * @var float
     */
    private $timeout = 10.0;

    /**
     * @var int
     */
    private $options = self::PROBLEM_AS_TRUE;

    /**
     * @var LoggerInterface|null
     */
    private $logger = null;

    /**
     * @var int
     */
    private $errorNumber = 0;

    /**
     * @var string
     */
    private $errorString = '';

    /**
     * Options for error handling.
     */
    public const EXCEPTION_ON_UNAVAILABILITY = 1;
    public const PROBLEM_AS_EXCEPTION = 2;
    public const PROBLEM_AS_TRUE = 4;

    /**
     * Checker constructor.
     *
     * @param string|null $hostname
     * @param int|null $port
     * @param int|null $options
     * @param float|null $timeout
     * @param LoggerInterface|null $logger
     */
    public function __construct(string $hostname = null, int $port = null, int $options = null, float $timeout = null, LoggerInterface $logger = null)
    {
        if ($hostname !== null) {
            $this->hostname = $hostname;
        }

        if ($port !== null) {
            $this->port = $port;
        }

        if ($timeout !== null) {
            $this->timeout = $timeout;
        }

        if ($options !== null) {
            $this->options = $options;
        }

        if ($logger !== null) {
            $this->logger = $logger;
        }
    }

    /**
     * Get the error number of the socket connection.
     *
     * @return int
     */
    public function getErrorNumber(): int
    {
        return (int)$this->errorNumber;
    }

    /**
     * Get the error string of the socket connection.
     *
     * @return string
     */
    public function getErrorString(): string
    {
        return (string)$this->errorString;
    }

    /**
     * Check the connection with the provided options.
     *
     * @return bool
     * @throws InternetException
     * @throws InternetProblemException
     */
    public function check(): bool
    {
        try {
            $connected = @fsockopen($this->hostname, $this->port, $this->errorNumber, $this->errorString, $this->timeout);

            if ($connected) {
                fclose($connected);

                return true;
            }

            if ($this->logger !== null) {
                $this->logger->alert('Internet not available: ' . $this->errorNumber . ' - ' . $this->errorString);
            }

            if ($this->options & self::EXCEPTION_ON_UNAVAILABILITY) {
                throw new InternetException($this->errorString, $this->errorNumber);
            }

            return false;
        } catch (InternetException $e) {
            throw $e;
        } catch (Exception $e) {
            if ($this->logger !== null) {
                $this->logger->debug('Something went wrong while trying to check internet availability: ' . $e->getMessage());
            }

            if ($this->options & self::PROBLEM_AS_EXCEPTION) {
                throw new InternetProblemException($this->errorString, $this->errorNumber);
            }

            return (bool) ($this->options & self::PROBLEM_AS_TRUE);
        }
    }

    /**
     * Convenience method with all default settings.
     *
     * @param bool $problemAsTrue
     * @return bool
     */
    public static function available(bool $problemAsTrue = true): bool
    {
        try {
            return (new self())->check();
        } catch (Exception $e) {
            // Assume something else went wrong.
            return $problemAsTrue;
        }
    }
}
