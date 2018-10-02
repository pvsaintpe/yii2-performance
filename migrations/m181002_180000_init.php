<?php

class m181002_180000_init extends \console\components\Migration
{

    /**
     * @return bool
     */
    public function safeUp()
    {
        $db = $this->getDb();
        parse_str(str_replace(';', '&', substr(strstr($db->dsn, ':'), 1)), $dsn);
        if (!array_key_exists('host', $dsn) || !array_key_exists('port', $dsn) || !array_key_exists('dbname', $dsn)) {
            return false;
        }

        $sql = file_get_contents(__DIR__ . '/data/m170501_000000_init.sql');
        if (YII_ENV_TEST) {
            $sql = preg_replace('~FOR EACH ROW\s+BEGIN~', 'FOR EACH ROW
thisTrigger: BEGIN
IF (@TRIGGER_CHECKS = FALSE) THEN
LEAVE thisTrigger;
END IF;', $sql);
        }

        $sql = preg_replace('~(^[-]{2,}.*$)~m', '{{SEPARATE_STAMP}}', $sql);

        $sqlParts = array_values(array_unique(array_map('trim', explode('{{SEPARATE_STAMP}}', $sql))));

        $setters = 'SET SESSION wait_timeout = 28800;' . PHP_EOL;
        $k = 0;
        foreach ($sqlParts as $key => $sqlPart) {
            if (empty($sqlPart)) {
                continue;
            }

            if (strpos($sqlPart, 'SET @OLD_UNIQUE_CHECKS') !== false) {
                $setters .= $sqlPart . PHP_EOL;
                continue;
            }

            $sqlQuery = explode('DELIMITER $$', $sqlPart);

            foreach ($sqlQuery as $sqlSubQuery) {
                $queries = explode('$$', $sqlSubQuery);

                foreach ($queries as $query) {
                    $k++;
                    $query = str_replace('DELIMITER ;', '', $query);
                    if (strpos($query, 'SET SQL_MODE') !== false) {
                        $setters .= $query . PHP_EOL;
                        continue;
                    }
                    $s = str_replace([
                        '`ff2`',
                        'DEFINER=`root`@`%`'
                    ], [
                        '`' . $dsn['dbname'] . '`',
                        'DEFINER = CURRENT_USER'
                    ], $setters . $query);
                    $this->execute($s);
                }
            }
        }
    }

    public function safeDown()
    {
        echo "m170501_000000_init cannot be reverted.\n";
        return false;
    }
}
