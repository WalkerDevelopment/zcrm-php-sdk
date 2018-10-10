<?php
namespace WalkerDevelopment\Zoho;

class ZohoOAuthPersistenceHandler implements ZohoOAuthPersistenceInterface
{
    private $type;
    private $host;
    private $port;
    private $database;
    private $username;
    private $password;

    public function __construct()
    {
        $this->type = env('DB_CONNECTION', 'pgsql');
        $this->host = env('DB_HOST', '127.0.0.1');
        $this->port = env('DB_HOST', '5432');
        $this->database = env('DB_DATABASE');
        $this->username = env('DB_USERNAME');
        $this->password = env('DB_PASSWORD');

        $this->getConnection();
    }
    public function saveOAuthData($zohoOAuthTokens)
    {
        try {
            self::deleteOAuthTokens($zohoOAuthTokens->getUserEmailId());

            $result = $this->query(sprintf("INSERT INTO oauthtokens (useridentifier, accesstoken, refreshtoken, expirytime) VALUES('%s', '%s', '%s', %s);", $zohoOAuthTokens->getUserEmailId(), $zohoOAuthTokens->getAccessToken(), $zohoOAuthTokens->getRefreshToken(), $zohoOAuthTokens->getExpiryTime()));

            if (!$result) {
                OAuthLogger::severe("OAuth token insertion failed");
            }
        } catch (Exception $ex) {
            Logger:severe("Exception occured while inserting OAuthTokens into DB(file::ZohoOAuthPersistenceHandler)({$ex->getMessage()})\n{$ex}");
        }
    }

    public function getOAuthTokens($userEmailId)
    {
        $oAuthTokens=new ZohoOAuthTokens();
        try {
            $result = $this->query("SELECT * FROM oauthtokens where useridentifier = '$userEmailId'");

            if (!$result) {
                OAuthLogger::severe("Getting result set failed");
                throw new ZohoOAuthException("No Tokens exist for the given user-identifier,Please generate and try again.");
            } else {
                $oAuthTokens->setExpiryTime($result[3]);
                $oAuthTokens->setRefreshToken($result[2]);
                $oAuthTokens->setAccessToken($result[1]);
                $oAuthTokens->setUserEmailId($result[0]);
            }
        } catch (Exception $ex) {
            OAuthLogger::severe("Exception occured while getting OAuthTokens from DB(file::ZohoOAuthPersistenceHandler)({$ex->getMessage()})\n{$ex}");
        }

        return $oAuthTokens;
    }

    public function deleteOAuthTokens($userEmailId)
    {
        try {
            $result = $this->query("DELETE FROM oauthtokens where useridentifier = '$userEmailId'");

            if (!$result) {
                OAuthLogger::severe("Deleting  oauthtokens failed");
            }
        } catch (Exception $ex) {
            OAuthLogger::severe("Exception occured while Deleting OAuthTokens from DB(file::ZohoOAuthPersistenceHandler)({$ex->getMessage()})\n{$ex}");
        }
    }

    private function query($query)
    {
        try {
            if ($this->type == 'pgsql') {
                $result = pg_query($this->connection, $query);

                if (!$result) {
                    OAuthLogger::severe("Query Error: " . pg_last_error($this->connection));
                }
                if (stripos($query, 'select')) {
                    $return = pg_fetch_assoc($result);
                }
            } else {
                $result = mysqli_query($this->connection, $query);

                if (!result) {
                    OAuthLogger::severe("Query Error: " . mysqli_error($this->connection));
                }
                if (stripos($query, 'select')) {
                    $return = mysqli_fetch_row($result);
                }
            }
        } catch (Exception $ex) {
            OAuthLogger::severe("Exception occured while getting OAuthTokens from DB(file::ZohoOAuthPersistenceHandler)({$ex->getMessage()})\n{$ex}");
        }

        return $return;
    }

    private function getConnection()
    {
        if ($this->type == 'pgsql') {
            $this->connection = pg_connect('dbname=' . $this->database . ' user=' . $this->username . ' password=' . $this->password . ' host=' . $this->host . ' port=' . $this->port);
        } else {
            $this->connection = new mysqli($this->host . ':' . $this->port, $this->username, $this->password, $this->database);
            if ($connection->connect_errno) {
                OAuthLogger::severe("Failed to connect to Database: (" . $connection->connect_errno . ") " . $connection->connect_error);
                echo "Failed to connect to Database: (" . $connection->connect_errno . ") " . $connection->connect_error;
            }
        }
    }
}
