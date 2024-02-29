<?php
	namespace App\Controller;
	
	class Logic {
		public $user = null;
		public $last_error = '';
		
		private $db  = null;
		
		public function __construct() {
			//
		}
		
		public function setdb($db) {
			$this->db = &$db;
		}
		
		public function setUser($user): void {
			$this->user = &$user;
		}

		public function getLastError(): string {
			return $this->last_error;
		}
		
		public function printAPIError($info = '') {
			exit(json_encode([
				'status' => 'error',
				'data'   => [],
				'error'  => $info
			]));
		}
		
		public function printAPISuccess($data = '') {
			exit(json_encode([
				'status' => 'success',
				'data'   => $data,
				'error'  => ''
			]));
		}

		function getUserIP(): string {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}

		function getUserAgentHash(): string {
			return hash('sha256', $_SERVER['HTTP_USER_AGENT']);
		}

		function getUserIPHash() {
			return hash('sha256', $this->getUserIP());
		}

		function checkUserCanGetBonus($by_UA = '', $by_IP = ''): bool {
			$sql_query = "SELECT id FROM vouchers WHERE by_IP='" . $by_IP . "' AND used_date >= CURDATE()";
			return $this->db->checkRowExists($sql_query);
		}

		function isVoucherExists($voucherCode): bool {
			$sql_query = "SELECT id FROM vouchers WHERE code='" . $voucherCode . "'";
			$result = $this->db->query2arr($sql_query);
			if(\count($result) == 0) {
				return false;
			}
			return true;
		}

		public function claimCryptonBonus(): string {
			$by_UA = $this->getUserAgentHash();
			$by_IP = $this->getUserIPHash();
			if($this->checkUserCanGetBonus($by_UA, $by_IP)) {
				$this->last_error = 'It looks like you already received a voucher for the last day';
				return '';
			}

			$sql_query = "SELECT id,code FROM vouchers WHERE used='0' ORDER BY id DESC";
			$result = $this->db->query2arr($sql_query);

			if(\count($result) == 0) {
				$this->last_error = 'The vouchers have run out. Come back later';
				return '';
			}

			$voucher_id   = $result['id'];
			$voucher_code = $result['code'];
			
			$sql_query = "UPDATE vouchers SET used='1', by_IP='" . $by_IP . "', by_UA='" . $by_UA . "' WHERE id=" . $voucher_id;
			if(! $this->db->tryQuery($sql_query)) {
				$this->last_error = 'Failed to get voucher code, try again later';
				return '';
			}
			
			return $voucher_code;
		}
		
		public function getFreshVouchersCount(): int {
			$sql_query = "SELECT COUNT(*) AS vouchersCount FROM vouchers WHERE used='0'";
			$result = $this->db->query2arr($sql_query);
			if($result == []) {
				return 0;
			}
			return $result['vouchersCount'];
		}

		function importVoucher($voucherCode): bool {
			if($voucherCode == '' || $voucherCode == 'code') {
				return true; // ignore empty code
			}

			if($this->isVoucherExists($voucherCode)) {
				return true; // already imported
			}

			$sql_query = "INSERT INTO vouchers SET code='" . $voucherCode . "'";
			if(! $this->db->tryQuery($sql_query)) {
				$this->last_error = 'failed to save voucher: ' . $voucherCode;
				return false;
			}
			return true;
		}

		public function importVouchers($vouchersPath): bool {
			if($vouchersPath == '') {
				$this->last_error = 'vouchers path is not set';
				return false;
			}

			$handle = fopen($vouchersPath, "r"); 
			$csvDelimiter = ';';

			while (($line = fgetcsv($handle, 0, $csvDelimiter)) !== FALSE) { 
				$voucherCode = $line[0];

				if(! $this->importVoucher($voucherCode)) {
					fclose($handle);
					return false;
				}
			}
			fclose($handle);

			return true;
		}
	}
