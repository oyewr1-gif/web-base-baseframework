<?php
class Validator {
    private $errors = [];

    // Membersihkan input secara rekursif dari celah XSS
    public static function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
            return $data;
        }
        
        if (is_string($data)) {
            // Hapus spasi berlebih, tag HTML/JS, dan ubah karakter spesial menjadi entitas aman
            $data = trim($data);
            $data = strip_tags($data);
            $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        return $data;
    }

    public function validate($data, $rules) {
        foreach ($rules as $field => $rulesArray) {
            $value = isset($data[$field]) ? trim((string)$data[$field]) : null;

            foreach ($rulesArray as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $this->errors[$field][] = "Bidang $field wajib diisi.";
                }
                
                if ($rule === 'numeric' && $value !== null && $value !== '' && !is_numeric($value)) {
                    $this->errors[$field][] = "Bidang $field harus berupa angka.";
                }

                if ($rule === 'email' && $value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "Format $field tidak valid.";
                }

                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if ($value !== null && strlen($value) < $min) {
                        $this->errors[$field][] = "Bidang $field minimal harus memiliki $min karakter.";
                    }
                }

                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if ($value !== null && strlen($value) > $max) {
                        $this->errors[$field][] = "Bidang $field maksimal $max karakter.";
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }
}
