<?php
/**
 * Seed 500 synthetic third parties and 500 synthetic products.
 *
 * This script is additive and can be run repeatedly. Existing records with
 * the generated references are skipped.
 */

require_once __DIR__.'/../../htdocs/master.inc.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

global $db, $user;

$adminUser = new User($db);
$adminUser->id = 1;
$adminUser->login = 'admin';

$createdThirdParties = 0;
$createdProducts = 0;

for ($index = 1; $index <= 500; $index++) {
	$thirdPartyRef = 'UPT-SOC-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT);
	$sqlThirdParty = "SELECT rowid FROM ".$db->prefix()."societe WHERE code_client = '".$db->escape($thirdPartyRef)."'";
	$resultThirdParty = $db->query($sqlThirdParty);
	if (!$resultThirdParty) {
		fwrite(STDERR, "Failed checking third party ".$thirdPartyRef.": ".$db->lasterror()."\n");
		exit(1);
	}

	if (!$db->fetch_object($resultThirdParty)) {
		$thirdParty = new Societe($db);
		$thirdParty->name = 'UPTown Demo Customer '.$index;
		$thirdParty->name_alias = 'UPT Demo '.$index;
		$thirdParty->code_client = $thirdPartyRef;
		$thirdParty->client = 1;
		$thirdParty->fournisseur = 0;
		$thirdParty->email = 'customer'.$index.'@example.test';
		$thirdParty->address = $index.' Demo Street';
		$thirdParty->zip = str_pad((string) (10000 + $index), 5, '0', STR_PAD_LEFT);
		$thirdParty->town = 'UPTown';
		$thirdParty->country_code = 'US';

		if ($thirdParty->create($adminUser) <= 0) {
			fwrite(STDERR, "Failed creating third party ".$thirdPartyRef.": ".$thirdParty->error."\n");
			exit(1);
		}
		$createdThirdParties++;
	}

	$productRef = 'UPT-PROD-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT);
	$sqlProduct = "SELECT rowid FROM ".$db->prefix()."product WHERE ref = '".$db->escape($productRef)."'";
	$resultProduct = $db->query($sqlProduct);
	if (!$resultProduct) {
		fwrite(STDERR, "Failed checking product ".$productRef.": ".$db->lasterror()."\n");
		exit(1);
	}

	if (!$db->fetch_object($resultProduct)) {
		$product = new Product($db);
		$product->ref = $productRef;
		$product->label = 'UPTown Demo Product '.$index;
		$product->description = 'Synthetic demo product for local testing.';
		$product->type = Product::TYPE_PRODUCT;
		$product->price = 10 + ($index % 100);
		$product->price_base_type = 'HT';
		$product->status = 1;
		$product->status_buy = 1;

		if ($product->create($adminUser) <= 0) {
			fwrite(STDERR, "Failed creating product ".$productRef.": ".$product->error."\n");
			exit(1);
		}
		$createdProducts++;
	}
}

print "Created ".$createdThirdParties." third parties and ".$createdProducts." products.\n";
