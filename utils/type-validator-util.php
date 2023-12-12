<?php
function validateData($data, $class) {
    if (!is_object($class)) {
        return false;
    }

    foreach ($data as $key => $value) {
      if ($class instanceof Course && $key === 'imageLink') {
        return true;
      }

        if (!property_exists($class, $key)) {
            return false;
        }

        $propertyType = $class->{$key};
        if (is_array($propertyType) && is_array($value)) {
            foreach ($value as $item) {
                if (!validateData($item, $propertyType[0])) {
                    return false;
                }
            }
        } elseif (is_object($propertyType) && is_array($value)) {
            if (!validateData($value, $propertyType)) {
                return false;
            }
        }
    }

    return true;
}
?>