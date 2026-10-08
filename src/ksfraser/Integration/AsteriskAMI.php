<?php

declare(strict_types=1);

namespace Ksfraser\Integration;

use Ksfraser\AsteriskPBX\Entity\AsteriskCall;
use Ksfraser\AsteriskPBX\Entity\SMSMessage;
use Ksfraser\AsteriskPBX\Entity\ClickToCallRequest;

class AsteriskAMI
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private $socket = null;
    private bool $connected = false;

    public function __construct(string $host, int $port, string $username, string $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect(): bool
    {
        $this->socket = @fsockopen($this->host, $this->port, $errno, $errstr, 5);
        
        if (!$this->socket) {
            return false;
        }

        stream_set_timeout($this->socket, 5);
        
        $response = $this->readResponse();
        
        if (strpos($response, 'Asterisk') === false) {
            $this->disconnect();
            return false;
        }

        $this->sendCommand("Action: Login\r\nAuthUser: {$this->username}\r\nAuthSecret: {$this->password}\r\nEvents: call,conference,dtmf\r\n\r\n");
        $response = $this->readResponse();

        $this->connected = strpos($response, 'Authentication accepted') !== false;
        
        return $this->connected;
    }

    public function disconnect(): void
    {
        if ($this->socket) {
            $this->sendCommand("Action: Logoff\r\n\r\n");
            fclose($this->socket);
            $this->socket = null;
        }
        $this->connected = false;
    }

    public function isConnected(): bool
    {
        return $this->connected;
    }

    public function originateCall(string $channel, string $extension, string $context = 'default', int $priority = 1): ?string
    {
        $uniqueId = uniqid('call_');
        
        $command = "Action: Originate\r\n".
                   "Channel: {$channel}\r\n".
                   "Context: {$context}\r\n".
                   "Exten: {$extension}\r\n".
                   "Priority: {$priority}\r\n".
                   "Variable: UNIQUEID={$uniqueId}\r\n".
                   "Async: true\r\n\r\n";
        
        $this->sendCommand($command);
        $response = $this->readResponse();

        return strpos($response, 'Originate succeeded') !== false ? $uniqueId : null;
    }

    public function hangup(string $channel): bool
    {
        $command = "Action: Hangup\r\nChannel: {$channel}\r\n\r\n";
        $this->sendCommand($command);
        $response = $this->readResponse();
        
        return strpos($response, 'Hungup') !== false;
    }

    public function sendDTMF(string $channel, string $digits): bool
    {
        $command = "Action: PlayDTMF\r\nChannel: {$channel}\r\nDigit: {$digits}\r\n\r\n";
        $this->sendCommand($command);
        $response = $this->readResponse();
        
        return strpos($response, 'DTMF tone sent') !== false;
    }

    public function transferCall(string $channel, string $extension, string $context = 'default'): bool
    {
        $command = "Action: Redirect\r\nChannel: {$channel}\r\nContext: {$context}\r\nExten: {$extension}\r\nPriority: 1\r\n\r\n";
        $this->sendCommand($command);
        $response = $this->readResponse();
        
        return strpos($response, 'Redirect succeeded') !== false;
    }

    public function getChannelStatus(string $channel): ?string
    {
        $command = "Action: ChannelStatus\r\nChannel: {$channel}\r\n\r\n";
        $this->sendCommand($command);
        $response = $this->readResponse();
        
        if (preg_match('/Status:\s*(\w+)/', $response, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    public function recordCall(string $channel, string $filename, int $duration = 0): bool
    {
        $command = "Action: MixMonitor\r\nChannel: {$channel}\r\n".
                   "File: {$filename}\r\n".
                   "Options: v()\r\n\r\n";
        
        $this->sendCommand($command);
        $response = $this->readResponse();
        
        return strpos($response, 'MixMonitor started') !== false;
    }

    public function stopRecording(string $channel): bool
    {
        $command = "Action: StopMixMonitor\r\nChannel: {$channel}\r\n\r\n";
        $this->sendCommand($command);
        $response = $this->readResponse();
        
        return strpos($response, 'MixMonitor stopped') !== false;
    }

    public function sendSMS(string $from, string $to, string $message): ?string
    {
        $command = "Action: SMS\r\n".
                   "Source: {$from}\r\n".
                   "Destination: {$to}\r\n".
                   "Body: {$message}\r\n\r\n";
        
        $this->sendCommand($command);
        $response = $this->readResponse();
        
        if (preg_match('/MessageId:\s*([^\s]+)/', $response, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    public function queueAdd(string $queue, string $channel, int $priority = 0): bool
    {
        $command = "Action: QueueAdd\r\nQueue: {$queue}\r\n".
                   "Interface: {$channel}\r\n".
                   "Priority: {$priority}\r\n\r\n";
        
        $this->sendCommand($command);
        $response = $this->readResponse();
        
        return strpos($response, 'Added interface') !== false;
    }

    public function queueRemove(string $queue, string $channel): bool
    {
        $command = "Action: QueueRemove\r\nQueue: {$queue}\r\nInterface: {$channel}\r\n\r\n";
        
        $this->sendCommand($command);
        $response = $this->readResponse();
        
        return strpos($response, 'Removed interface') !== false;
    }

    public function getQueueStatus(string $queue): ?array
    {
        $command = "Action: QueueStatus\r\nQueue: {$queue}\r\n\r\n";
        
        $this->sendCommand($command);
        $response = $this->readResponse();
        
        $status = [];
        if (preg_match_all('/Queue:\s*(\w+).*?Calls:\s*(\d+).*?HoldTime:\s*([\d.]+).*?Max:\s*(\d+)/s', $response, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $status[] = [
                    'queue' => $match[1],
                    'calls' => $match[2],
                    'hold_time' => $match[3],
                    'max' => $match[4],
                ];
            }
        }
        
        return empty($status) ? null : $status;
    }

    private function sendCommand(string $command): void
    {
        if ($this->socket) {
            fwrite($this->socket, $command);
        }
    }

    private function readResponse(): string
    {
        $response = '';
        
        if ($this->socket) {
            while (!feof($this->socket)) {
                $line = fgets($this->socket);
                $response .= $line;
                
                if ($line === "\r\n" || strpos($line, 'Response:') !== false) {
                    break;
                }
            }
        }
        
        return $response;
    }
}