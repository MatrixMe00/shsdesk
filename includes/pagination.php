<?php
    /**
     * This function does a general pagination
     * 
     * @param mysqli $connect The database connection adapter
     * @param string $sql The sql command to run
     * @param int $per_page The number of results to return per page
     * @param int $page_number The current page to fetch 
     * @param string $main_table Should be used if you want to get the total number of pages returned. If table has an alias, use it in this way "table_name alias"
     * @param array $main_table_where a where condition to be used when the main_table is provided
     * @abstract main_table_where arrays can be in this form [[column_name, value]] or [[column_name, operator, value]] or [[full_statement]]
     * @return mixed Returns null if there are no results or [data=>array, total_pages=>int]
     */
    function paginate(mysqli $connect, string $sql, int $per_page, int $page_number = 1, string $main_table = "", ?array $main_table_where = null){
        $response = null;

        // if the limit has not already been parsed, then parse the limit
        if(strpos(strtolower($sql), " limit ") === false){
            $sql .= create_limit_string($page_number, $per_page, true);
        }
        $results = $connect->query($sql);

        if($results->num_rows > 0){
            $results = $results->fetch_all(MYSQLI_ASSOC);

            // get the total rows in the main table
            if($main_table){
                $where = $main_table_where ? format_wheres($main_table_where, $main_table) : "";
                $total_rows = $connect->query("SELECT COUNT(*) as total FROM $main_table $where")->fetch_assoc()["total"];

                // get the total pages
                $total_pages = ceil($total_rows/$per_page);
            }

            $response = [
                "data" => $results,
                "total_pages" => $total_pages ?? 0
            ];
        }

        return $response;
    }

    /**
     * Makes pagination specifically for data tables. Inherits properties of the general pagination but differes in response
     * 
     * @param mysqli $connect The database connection adapter
     * @param string $sql The sql command to run
     * @param int $per_page The number of results to return per page
     * @param int $page_number The current page to fetch 
     * @param string $main_table Should be used if you want to get the total number of pages returned. If table has an alias, use it in this way "table_name alias"
     * @param array $main_table_where a where condition to be used when the main_table is provided
     * @abstract main_table_where arrays can be in this form [[column_name, value]] or [[column_name, operator, value]] or [[full_statement]]
     * @return mixed Returns null if there are no results or [data=>array, total_pages=>int]
     */
    function datatable_paginate(mysqli $connect, string $sql, int $per_page, int $page_number = 1, string $main_table = "", ?array $main_table_where = null, string $order_by = "DESC", string $order_by_key = ""){
        $response = null;

        // if it doesnt have the order by clause, add it
        if(strpos(strtolower($sql), " order by ") === false && !empty($order_by_key)){
            $sql .= " ORDER BY $order_by_key $order_by";
        }

        // if the limit has not already been parsed, then parse the limit
        if(strpos(strtolower($sql), " limit ") === false){
            $sql .= create_limit_string($page_number, $per_page, true);
        }
        $results = $connect->query($sql);

        if($results->num_rows > 0){
            $results = $results->fetch_all(MYSQLI_ASSOC);

            // get the total rows in the main table
            if($main_table){
                $where = $main_table_where ? format_wheres($main_table_where, $main_table) : "";
                $total_rows = $connect->query("SELECT COUNT(*) as total FROM $main_table $where")->fetch_assoc()["total"];

                // get the total pages
                $total_pages = ceil($total_rows/$per_page);
            }

            $response = [
                "data" => $results,
                "total_records" => $total_rows ?? 0,
                "total_pages" => $total_pages ?? null
            ];
        }

        return $response;
    }

    /**
     * This creates a limit statement based on page number and page size
     * @param int $page_number The page number
     * @param int $page_size The maximum number of results per page
     * @param bool $initialize_space This is used to tell if it should start with a space or not. False by default
     * @return string
     */
    function create_limit_string(int $page_number, int $page_size, bool $initialize_space = false){
        $start_row = ($page_number - 1) * $page_size;
        $space = $initialize_space ? " " : "";

        return $space."LIMIT $start_row, $page_size";
    }

    /**
     * Creates a where statement for the main_table query
     * @param ?array $where An array of wheres.
     * @param string $table_name The name of the table. This is to ensure the table column is picked appropriately
     * @return string
     */
    function format_wheres(array $where, string $table_name){
        $statement = "";

        if($where){
            $operators = [
                "=", "is", "is not", "<", "<=", ">", ">=", "like"
            ];

            if(strpos($table_name, " ") !== false){
                $table_name = explode(" ", $table_name);
                $table_name = $table_name[1];
            }

            $statement = [];
            
            // leave it in the format array(numeric_key => array()) format
            if(!array_key_exists(0, $where)){
                $where = [$where];
            }
    
            foreach($where as $w){
                // some statements might be in their string formats
                if(!is_array($w)){
                    if(!empty($w)){
                        $statement[] = $w;
                    }
                }else{
                    if(count($w) == 3){
                        list($column, $operator, $value) = $w;
                    }elseif(count($w) == 2){
                        list($column, $value) = $w;
                    }else{
                        $w = $w[0];
                        if(!empty($w)){
                            $statement[] = $w;
                        }

                        // move to next
                        continue;
                    }
    
                    // operator should be an equal to by default
                    if(!isset($operator) || !in_array(strtolower($operator), $operators)){
                        $operator = "=";
                    }

                    // all string values should be put in single quotes
                    if(!is_integer($value)){
                        $value = "'$value'";
                    }
                    
                    // if the column doesnt have a parent attached already, then use this
                    if(strpos($column, ".") === false){
                        $column = "$table_name.$column";
                    }

                    // add to list of statements
                    $statement[] = "$column $operator $value";
                }
            }

            $statement = "WHERE " . implode(" AND ", $statement);
        }

        return $statement;
    }