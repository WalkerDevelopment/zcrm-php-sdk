<?php
namespace WalkerDevelopment\Zoho;

interface ZohoOAuthPersistenceInterface
{
    public function saveOAuthData($zohoOAuthTokens);
    public function getOAuthTokens($userEmailId);
    public function deleteOAuthTokens($userEmailId);
}
