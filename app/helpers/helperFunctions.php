<?php

    class helperFunctions {
        public function returnData($status,$message, $data) {
            return json_encode(array(
                'status' => $status,
                'message' => $message,
                'data' => $data
            ));
        }
     }

?>