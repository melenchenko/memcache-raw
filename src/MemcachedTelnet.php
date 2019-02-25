<?php
ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
error_reporting(E_ALL); // E_ALL - отображаем ВСЕ ошибки

use \Graze\TelnetClient\TelnetClient;
use \Graze\TelnetClient\TelnetResponse;

class MemcachedTelnet extends TelnetClient {
    protected function getResponse($prompt = null)
    {
        $isError = false;
        $buffer = '';
        do {
            // process one character at a time
            try {
                $character = $this->socket->read(1);
            } catch (Exception $e) {
                throw new TelnetException('failed reading from socket', 0, $e);
            }

            if (in_array($character, [$this->NULL, $this->DC1])) {
                break;
            }

            if ($this->interpretAsCommand->interpret($character, $this->socket)) {
                continue;
            }

            $buffer .= $character;

            // check for prompt
            if ($this->promptMatcher->isMatch($prompt ?: $this->prompt, $buffer, "END\r\n")) {
                break;
            }

            // check for error prompt
            if ($this->promptMatcher->isMatch($this->promptError, $buffer, $this->lineEnding)) {
                $isError = true;
                break;
            }
        } while (true);

        return new TelnetResponse(
            $isError,
            $this->promptMatcher->getResponseText(),
            $this->promptMatcher->getMatches()
        );
    }
}