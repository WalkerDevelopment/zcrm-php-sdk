<?php
namespace WalkerDevelopment\Zoho;

class ZCRMRestClient
{
    public static function getInstance()
    {
        return new ZCRMRestClient();
    }

    public static function initialize($configuration = null)
    {
        ZCRMConfigUtil::initialize(true, $configuration);
    }

    public function getAllModules()
    {
        return MetaDataAPIHandler::getInstance()->getAllModules();
    }

    public function getModule($moduleName)
    {
        return MetaDataAPIHandler::getInstance()->getModule($moduleName);
    }

    public function getOrganizationInstance()
    {
        return ZCRMOrganization::getInstance();
    }

    public function getModuleInstance($moduleAPIName)
    {
        return ZCRMModule::getInstance($moduleAPIName);
    }

    public function getRecordInstance($moduleAPIName, $entityId)
    {
        return ZCRMRecord::getInstance($moduleAPIName, $entityId);
    }

    public function getCurrentUser()
    {
        return OrganizationAPIHandler::getInstance()->getCurrentUser();
    }

    public static function getCurrentUserEmailID()
    {
        return isset($_SERVER[APIConstants::USER_EMAIL_ID])?$_SERVER[APIConstants::USER_EMAIL_ID]:null;
    }

    public static function getOrganizationDetails()
    {
        return OrganizationAPIHandler::getInstance()->getOrganizationDetails();
    }
}
