<?php
    /**
     * Core cURL request function.
     *
     * @param string $url
     * @param string $method GET|POST|PUT|DELETE
     * @param array|string|null $data
     * @param array $headers
     * @return array ['status' => int, 'response' => string, 'error' => string|null]
     */
    function curl_request(string $url, string $method = 'GET', $data = null, array $headers = []): array {
        $ch = curl_init();
        $method = strtoupper($method);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? json_encode($data) : $data);
                }
                break;
            case 'PUT':
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
                }
                break;
            case 'GET':
            default:
                if (!empty($data) && is_array($data)) {
                    $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($data);
                    curl_setopt($ch, CURLOPT_URL, $url);
                }
                break;
        }

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
        } else {
            $error = null;
        }

        curl_close($ch);

        return [
            'status'   => $status,
            'response' => json_decode($response, true) ?? $response,
            'error'    => $error,
        ];
    }

    /**
     * Convenience wrapper: GET request
     */
    function curl_get(string $url, array $params = [], array $headers = []): array {
        return curl_request($url, 'GET', $params, $headers);
    }

    /**
     * Convenience wrapper: POST request
     */
    function curl_post(string $url, $data = null, array $headers = []): array {
        return curl_request($url, 'POST', $data, $headers);
    }

    /**
     * Convenience wrapper: PUT request
     */
    function curl_put(string $url, $data = null, array $headers = []): array {
        return curl_request($url, 'PUT', $data, $headers);
    }

    /**
     * Convenience wrapper: DELETE request
     */
    function curl_delete(string $url, $data = null, array $headers = []): array {
        return curl_request($url, 'DELETE', $data, $headers);
    }
