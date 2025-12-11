<?php
/**
 * FormHandler Class
 * Handles form validation and sanitization
 */
class FormHandler {
    /**
     * Sanitize input string
     */
    public static function sanitizeString($input, $maxLength = null) {
        if (!is_string($input)) {
            return '';
        }

        // Trim whitespace
        $input = trim($input);

        // Limit length if specified
        if ($maxLength !== null && strlen($input) > $maxLength) {
            $input = substr($input, 0, $maxLength);
        }

        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize email
     */
    public static function sanitizeEmail($email) {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    }

    /**
     * Sanitize phone number (removes special chars, keeps digits)
     */
    public static function sanitizePhone($phone) {
        $phone = preg_replace('/[^0-9+\-\s]/', '', $phone);
        return trim($phone);
    }

    /**
     * Sanitize numeric input
     */
    public static function sanitizeNumber($input, $type = 'int') {
        if ($type === 'float') {
            return floatval(filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        }
        return intval(filter_var($input, FILTER_SANITIZE_NUMBER_INT));
    }

    /**
     * Validate required field
     */
    public static function validateRequired($value, $fieldName = '') {
        if (empty(trim($value))) {
            return "The {$fieldName} field is required.";
        }
        return '';
    }

    /**
     * Validate email field
     */
    public static function validateEmail($email) {
        if (empty($email)) {
            return 'Email is required.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email format.';
        }

        return '';
    }

    /**
     * Validate password strength
     */
    public static function validatePassword($password) {
        if (empty($password)) {
            return 'Password is required.';
        }

        if (strlen($password) < 6) {
            return 'Password must be at least 6 characters long.';
        }

        return '';
    }

    /**
     * Validate numeric range
     */
    public static function validateNumberRange($value, $min, $max, $fieldName = '') {
        $num = floatval($value);
        
        if ($num < $min) {
            return "{$fieldName} must be at least {$min}.";
        }

        if ($num > $max) {
            return "{$fieldName} must not exceed {$max}.";
        }

        return '';
    }

    /**
     * Validate string length
     */
    public static function validateLength($value, $minLength, $maxLength, $fieldName = '') {
        $length = strlen(trim($value));

        if ($length < $minLength) {
            return "{$fieldName} must be at least {$minLength} characters long.";
        }

        if ($length > $maxLength) {
            return "{$fieldName} must not exceed {$maxLength} characters.";
        }

        return '';
    }

    /**
     * Validate array of form data
     * @param array $data - Form data
     * @param array $rules - Validation rules ['field' => ['type' => 'email', 'required' => true, ...]]
     * @return array - Errors array, empty if all valid
     */
    public static function validateForm($data, $rules) {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? '';

            // Check required
            if (!empty($rule['required']) && empty($value)) {
                $fieldLabel = $rule['label'] ?? ucfirst($field);
                $errors[$field] = "{$fieldLabel} is required.";
                continue;
            }

            if (empty($value)) {
                continue;
            }

            // Validate type
            if (!empty($rule['type'])) {
                switch ($rule['type']) {
                    case 'email':
                        $error = self::validateEmail($value);
                        if ($error) $errors[$field] = $error;
                        break;

                    case 'password':
                        $error = self::validatePassword($value);
                        if ($error) $errors[$field] = $error;
                        break;

                    case 'phone':
                        if (strlen($value) < 10) {
                            $errors[$field] = 'Phone number must be at least 10 digits.';
                        }
                        break;

                    case 'number':
                        if (!is_numeric($value)) {
                            $errors[$field] = 'Must be a number.';
                        }
                        break;

                    case 'string':
                        if (isset($rule['minLength']) || isset($rule['maxLength'])) {
                            $error = self::validateLength(
                                $value,
                                $rule['minLength'] ?? 0,
                                $rule['maxLength'] ?? 255,
                                $rule['label'] ?? ucfirst($field)
                            );
                            if ($error) $errors[$field] = $error;
                        }
                        break;
                }
            }

            // Validate range
            if (isset($rule['min']) && isset($rule['max'])) {
                $error = self::validateNumberRange(
                    $value,
                    $rule['min'],
                    $rule['max'],
                    $rule['label'] ?? ucfirst($field)
                );
                if ($error) $errors[$field] = $error;
            }
        }

        return $errors;
    }

    /**
     * Get POST data safely
     */
    public static function getPostData() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [];
        }

        return $_POST;
    }

    /**
     * Get JSON POST data
     */
    public static function getJSONData() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [];
        }

        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : [];
    }
}
?>
