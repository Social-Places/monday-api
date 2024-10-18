<?php

namespace MondayAPI;

class MondayAPI
{
    private $APIV2_Token;
    private $API_Url = "https://api.monday.com/v2/";
    private $debug = false;
    private $error = null;
    
    const TYPE_QUERY = 'query';
    const TYPE_MUTAT = 'mutation';

    function __construct(bool $debug = false) {
        $this->debug = $debug;
    }

    private function printDebug($print) {
        echo '<div style="background: #f9f9f9; padding: 20px; position: relative; border: solid 1px #dedede;">
        ' . $print . '
        </div>';
    }

    public function setToken(Token $token) {
        $this->APIV2_Token = $token;
        return $this;
    }

    private function content($type, $request) {
        $request = $this->replaceUnwantedCharacters($request);
        if ($this->debug) {
            $this->printDebug($type . ' { ' . $request . ' } ');
        }
        return json_encode(['query' => $type . ' { ' . $request . ' } ']);
    }

    protected function request($type = self::TYPE_QUERY, $request = null) {
        set_error_handler(
            function ($severity, $message, $file, $line) {
                throw new \ErrorException($message, $severity, $severity, $file, $line);
            }
        );

        try {
            $headers = [
                'Content-Type: application/json',
                'User-Agent: [Tblack-IT] GraphQL Client',
                'Authorization: ' . $this->APIV2_Token->getToken()
            ];

            $data = @file_get_contents($this->API_Url, false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => $headers,
                    'content' => $this->content($type, $request),
                ]
            ]));
            return $this->response($data);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    protected function response($data) {
        if (!$data)
            return false;

        $json = json_decode($data, true);

        if (isset($json['data'])) {
            return $json['data'];
        } else if (isset($json['errors']) && is_array($json['errors'])) {
            return $json['errors'];
        }

        return false;
    }

    private function replaceUnwantedCharacters($string) {
        /* If this becomes
          'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A',
            'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O',
            'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a',
            'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i',
            'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
            'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y',
         */

        $unwanted_array = ['﻿' => '', ' ' => ' ', '⦃' => '{', '〜' => '~', '〝' => '"', '〞' => '"', '〛' => ']', '〉' => '>', '〈' => '<', '«' => '"',
            '­' => '-', '´' => '\'', '»' => '"', '÷' => '/', 'ǀ' => '|', 'ǃ' => '!', 'ʹ' => '\'', 'ʺ' => '"', 'ʼ' => '\'', '˄' => '^', 'ˆ' => '^', 'ˈ' => '\'',
            'ˋ' => '`', 'ˍ' => '_', '˜' => '~', '̀' => '`', '́' => '\'', '̂' => '^', '̃' => '~', '̋' => '"', '̎' => '"', '̱' => '_', '̲' => '_', '̸' => '/',
            '։' => ':', '׀' => '|', '׃' => ':', '٪' => '%', '٭' => '*', '‐' => '-', '‑' => '-', '‒' => '-', '–' => '-', '—' => '-', '―' => '-', '‖' => '|',
            '‗' => '_', '‘' => '\'', '’' => '\'', '‚' => ',', '‛' => '\'', '“' => '"', '”' => '"', '„' => '"', '‟' => '"', '′' => '\'', '″' => '"', '‴' => '\'',
            '‵' => '`', '‶' => '"', '‷' => '\'', '‸' => '^', '‹' => '<', '›' => '>', '‽' => '?', '⁄' => '/', '⁎' => '*', '⁒' => '%', '⁓' => '~', '⁠' => ' ',
            '⃥' => '\\', '−' => '-', '∕' => '/', '∖' => '\\', '∗' => '*', '∣' => '|', '∶' => ':', '∼' => '~', '≤' => '<', '≥' => '>', '≦' => '<', '≧' => '>',
            '⌃' => '^', '〈' => '<', '〉' => '>', '♯' => '#', '✱' => '*', '❘' => '|', '❢' => '!', '⟦' => '[', '⟨' => '<', '⟩' => '>', '⦄' => '}', '〃' => '"'];
        return str_replace("\'", "'", strtr($string, $unwanted_array));
    }
}
