<?php
class Validator {
    private $errors = [];

    public function validate($data, $rules) {
        foreach ($rules as $field => $rulesArray) {
            $value = isset($data[$field]) ? trim($data[$field]) : null;

            foreach ($rulesArray as $rule) {
                // Rule: required
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $this->errors[$field][] = "Bidang $field wajib diisi.";
                }
                
                // Rule: numeric
                if ($rule === 'numeric' && $value !== null && !is_numeric($value)) {
                    $this->errors[$field][] = "Bidang $field harus berupa angka.";
                }

                // Rule: min:x (misal min:3)
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if ($value !== null && strlen($value) < $min) {
                        $this->errors[$field][] = "Bidang $field minimal harus memiliki $min karakter.";
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
