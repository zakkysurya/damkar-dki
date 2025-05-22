<?php

if (!function_exists('sendErrorResponse')) {
      /**
       * Periksa apakah user memiliki permission yang diberikan.
       *
       * @param string $permission
       * @return bool
       */
      function sendErrorResponse($messages, $statusCode)
      {
            ## Validasi messages
            $fix_messages = '';
            if (is_array($messages)) {
                  $fix_messages = implode('<br/>', Arr::flatten($messages));
            } else {
                  $fix_messages = (string) $messages;
            }
            ## Jika error pada sql, biasanya statusCode berupa string
            if (is_string($statusCode) || empty($statusCode)) {
                  $statusCode = 404;
            }
            ## Setting response
            return response()->json([
                  'status' => false,
                  'message' => $fix_messages,
            ], $statusCode);
      }
}
