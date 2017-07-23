<?php
namespace SaltedHerring;

class SaltedSearch
{
    public static function RawSearch($tables, $fields, $terms)
    {
        $mode = ' IN BOOLEAN MODE';

        if (is_array($fields)) {
            $fields = implode(',', $fields);
        }

        $sqlQuery = new \SQLSelect();
        $sqlQuery->setFrom($tables);
        $sqlQuery->addSelect($fields);
        $sqlQuery->addWhere("MATCH( $fields ) AGAINST ('$terms'$mode)");

        $result = $sqlQuery->execute();
        $result_data = [];

        foreach ($result as $item)
        {
            $data = [];
            foreach ($item as $key => $value)
            {
                $data[$key] = $value;
            }

            $result_data[] = new \ArrayData($data);
        }

        return new \ArrayList($result_data);
    }

    public static function Search($ClassName, $fields, $terms)
    {
        $mode = ' IN BOOLEAN MODE';

        if (is_array($fields)) {
            $query = '';
            foreach($fields as $field)
            {
                $query .= "`$ClassName`.`$field`";
                $query .= ",";
            }

            $fields = rtrim($query, ",");
        }

        return $ClassName::get()->where("MATCH( $fields ) AGAINST ('$terms'$mode)");
    }
}
