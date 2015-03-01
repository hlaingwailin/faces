<?php



class Model_Constants_DataMap
{

   public static $COMPANIES_MAP = array("Shop Operator Name" => "Sop_Name", "Mailing Address Line 1" => "Sop_MailingAddress1", "Mailing Address Line 2" => "Sop_MailingAddress2", "Postal Code" => "Sop_PostalCode", "Giro Account No" => "Sop_GiroAccountNo", "Attention" => "Sop_Attention", "Main Contact Name" => "Sop_MainContactPerson");

   public static $TENANTS_MAP = array("Customer ID" => "Sho_CustomerId", "Tenant Name" => "Sho_Name", "Payment Term" => "Sho_PaymentTerm", "Meter ID" => "Sho_MeterId", "Premise Address" => "Sho_PremiseAddress", "Building Name-Bul_Id_Name" => "Sho_BuildingId", "Operator Name-Sop_Id_Name" => "Sho_OperatorId", "Terminated" => "Sho_Terminated");

   public static $BUILDINGS_MAP = array("Building Name" => "Bul_Name", "Address 1" => "Bul_Address1", "Address 2" => "Bul_Address2", "Postal Code" => "Bul_PostalCode", "Operator Name-Buo_Id_RegistrationNo" => "Bul_OperatorId");

   public static $BUILDINGS_OPERATOR_MAP = array("Building Operator Name" => "Buo_Name", "Address 1" => "Buo_Address1", "Address 2" => "Buo_Address2", "Postal Code" => "Buo_PostalCode", "Company Reg No" => "Buo_RegistrationNo", "GST Reg No" => "Buo_GSTRegistrationNo");

   public static $RETAILER_MAP = array("Retailer Name" => "Ret_Name", "Address 1" => "Ret_Address1", "Address 2" => "Ret_Address2", "Postal Code" => "Ret_PostalCode", "Company Reg No" => "Ret_RegistrationNo", "Telephone" => "Ret_Phone", "Facsimile" => "Ret_Facsimile");

   public static $DEPOSIT_MAP = array("Customer ID-Sho_Id_CustomerId" => "Dep_ShopId", "Deposit Amount" => "Dep_Amount", "Deposit Type" => "Dep_Type");

   public static $METER_MAP = array("Customer ID" => "Met_CustomerId", "Meter ID" => "Met_MeterSerialNumber", "Last Read Usage" => "Met_LastReadUsage", "Last Read Date" => "Met_LastReadDate", "Current Read Usage" => "Met_CurrentReadUsage", "Current Read Date" => "Met_CurrentReadDate", "Previous Usage" => "Met_PreviousUsage", "Previous Usage End Date" => "Met_PreviousUsageEndDate", "Current Usage" => "Met_CurrentUsage", "Current Usage End Date" => "Met_CurrentUsageEndDate", "Batch Number" => "Met_BatchId");

   public static $PAYMENTS_MAP = array("Invoice Number" => "Pay_InvoiceNumber", "Payment Amount" => "Pay_Amount", "Date Of Payment" => "Pay_PaymentDate");

   // table prefix to model map
   public static $PREFIX_TABLE_MAP = array("Bul" => "Model_DbTable_Buildings", "Buo" => "Model_DbTable_BuildingOperators","Dep" => "Model_DbTable_Deposits", "Met" => "Model_DbTable_MeterData", "Ret" => "Model_DbTable_Retailers", "Sop" => "Model_DbTable_Companies", "Sho" => "Model_DbTable_Shops", "Usr" => "tbl_users", "Agn" => "Model_DbTable_BillingAgents", "Rat" => "Model_DbTable_Rates", "Pay" => "Model_DbTable_Payments", "Inv" => "Model_DbTable_Invoices");

   public static $PREFIX_TO_ENTITY_MAP = array("Bul" => "buildings", "Buo" => "buildingoperators", "Dep" => "deposits", "Met" => "meterdata", "Ret" => "retailers", "Sop" => "shopoperators", "Sho" => "shops", "Usr" => "users", "Rat" => "meterrates", "Agn" => "billingagents", "Pay" => "payments", "Inv" => "invoices");

   public static $INVALID_COLUMN_NAMES = array("submit","edit","save","invoice");

}

?>