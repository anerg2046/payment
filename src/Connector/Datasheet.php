<?php
namespace anerg\Payment\Connector;

class Datasheet
{
    protected static $_data = [];
    protected static $_alias = [];

    public static function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                self::set($k, $v);
            }
        } else {
            $name = self::alias($name) . '___' . get_called_class();
            self::$_data[$name] = $value;
        }
    }

    public static function get($name, $default = null)
    {
        if (is_array($name)) {
            foreach ($name as $v) {
                $ret[$v] = self::get($v);
            }
        } else {
            $name = self::alias($name) . '___' . get_called_class();
            $ret = (isset(self::$_data[$name]) && !empty(self::$_data[$name])) ? self::$_data[$name] : $default;
        }
        return $ret;
    }

    public static function setAlias(array $alias = [])
    {
        self::$_alias = $alias;
    }

    public static function alias(string $name)
    {
        return isset(self::$_alias[$name]) ? self::$_alias[$name] : $name;
    }

    public static function all()
    {
        $data = self::$_data;
        $return = [];
        foreach ($data as $k => $v) {
            if (strpos($k, '___' . get_called_class()) !== false) {
                $k = str_replace('___' . get_called_class(), '', $k);
                $return[$k] = $v;
            }
        }
        return $return;
    }
}
