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

			// TODO: move to env?
			$banned = [
				"ff05be18c8ff2640641ff6ce4bc4c6174adc209bcfe15b1200661c0793fd7975",
				"c606f2ba3262c7e89dcadd4599b2241ebd51e80c89a34a4e4590dc6440b4602d",
				"fef1892566463876586aa05858928f3b830ef30337f5fb9f02c476d426377b16",
				"00585ae2d75bed9efc9d06052ceae7286d6a3d2b08224f3932a84488c072e6d8",
				"00d0f6ceebc9728a99180fefbbf653f55f0cbf184d7725fdae41a6302aa7c727",
				"024d6ac84bebabe918697bd1aa1d84de4296b117ac8b1192a6fe477633c1a6b3",
				"0544b3506df29ae49147ea155b3d98b4dce9c7bf77fee21a53b7f2cd21eccfcb",
				"0890f41abcc871bee131ab99f1b2280ed1a1a1fb82a678640e0057cf6185c98a",
				"11217423d83aeac5a5314b3b17ec516a626d08601087d62127a772e68de78667",
				"197ed3736c093deb15021d2e2aa23efeeaa28aa4bbde5418dd9aaf3653a777c9",
				"2023bb25e40b0e7bb0d0684277a73efd908efa9381bd67731a5c5180cc5d469e",
				"20d5644a50d89647a637ebf285f5474fa6cc8854f077d165fa755eaea0bfdecf",
				"2162627bf44d74b65c199c7d25ded78ed7949a6c3931b90a8c92c58b0ff57d21",
				"241153ffd53d03a3650a6197c05ed059fd811c480b8b4884542588ef0d048b93",
				"2a938017ac23f95809748c113fd5d5509f6ad454a58f99286b386f5679e0ceb1",
				"2da0f3d2c21376a9560ac0bd4ff10665a4be6c39858b73b434a9c76f6f757faf",
				"3cab633aaf36e7a01acb74183b27c3cda45f2b086fc4cb6d26d705339595db2d",
				"3d82af94d72e129d4be6502a35e591cd292168e612d702a763083153fa73d96f",
				"3db3ff5e617d43dc3739a32ed02c69b3939aec361721b393e6ecc3f6e029590c",
				"3db50df2b1fdf979858475b76dd2950b1de73cf11aa55d2c5746840fd8eb6848",
				"3e0694a123b7dde308341d22e97ecc7d9325d276d67c777e24b8214806a35ed8",
				"3f8354a3ba8345890fe90f4cac4df5e40f75ca2e53e2b98dc9341ae98871673f",
				"409f0b97caf4636fe4bc134c555cdbd94c7fc1b7d701e85ec8e5660f7afc341a",
				"442691aa05eb67463597db16b5cdd46981e912ac4a9946fa3e2604b4dd565c79",
				"45e1ff30e5e2043c4f8febc84990ff92d6a2c4080a31d3461d277b8b0a481862",
				"4a1e6cf3066c190a07d4f00b63f9c74233e19c3b5c925a909b3a117c4dc4117c",
				"4d44cf205e3c708edb26aebb23ebc10448ac99cbc56c3677d2853364b95e09e3",
				"5445915a3b1eae8059b2f94a0d7bec5ed24d4ad265f802e6dd8664d1c4cc758a",
				"56080291a47ee3ae8fb7cda3c7c2a06a60d64e299ba4abeb7ad0ab5f4f04ecb5",
				"6031a1363aeaf01856844cdd354199a85495392143a0e99dfe952b10590c03cd",
				"6bc4784d0d57990d1c3f615b898badef003a8d0fd59631c08e568e8e8d3bb460",
				"6ca35f2c0cb94f6eb1d844493e54d7b978ad5f215f6db76d20c847d87a6077b0",
				"6e3cc951435cbb8825261e4d95373b1b86c726a407bd8293b12717b06655aed1",
				"6e7e1daee6454f9d3381620c28d97b7ae520b93fd8da06ed6e69728a0d9f743e",
				"6f488555f14ae2a98ff30f27c803bfba0aeb94cb3515ef79635b298e5cb52910",
				"6fd0ae54a4274d3f99127657d22c523366a9d314d2f93bfdfe6d98fa27015f2c",
				"75ff83dde080349f6abc7ed35343294144b8d2bbfbf5e524cd5de0a8a7f71286",
				"76a518df55ad2d8bb69b86b21c1216a797aed392777658b8468def3c21df5875",
				"7d51244fa651952dd1e8a4b52280993db977247431b43ac1b6afb3704f4e8704",
				"7df9d37faae4293be4eb0bb356e7b4cf634db27eb4f6169a80f6579563b74ac8",
				"811a490c86217dee2ed2e1509942f438f17b28e2f539e5a559c79dd3036da198",
				"88bce3e65fe3d821dfc50c78d450975bf2cda392f5b79fb8cfeaaa8b718a878e",
				"8db7aaec44788f62665eaecf70c0806865bab82d364781074ca037e061c4584c",
				"922821bf8147ddc1fd03240a2cef0f4053de1224040b7dcc669213a7bfe76421",
				"9470f30f7858daaa72ffc329b7ccbfb50f29fc3b6b85d20546c8a74db38af7a3",
				"97d722a1e2c1a32753266eb7e32dc7f2e20c5ae141d42d5f90d49b40a785410b",
				"97ee7be6751d8c99f8ce014f0fa47fbf179cd202cc1e3a6e9693a2c0bf5e00c9",
				"9be8fa44d35ff7f151c37036e88e774a76b5d32f5485479263c4b36080cf350d",
				"a116bd2d4d26129f8bf25c5eac7323cea561bb67d59a5439c87b22d0289dc1af",
				"a18f21da85caa67cdc03fc81babe6dbbe738a9dfb472376bcbec02ef0c42e8ef",
				"aaaade76a9dc4a3dc57233805fb94634a77b928defe96924d7cb177da87d52d7",
				"aeb6f71a1abfe808f059b5d7f085309eb9901211aab7841f8f27a40076d22d2a",
				"c335e6f05fe572a1dc062131377d167bcb6019632be6f41098cd13af71937a30",
				"c606f2ba3262c7e89dcadd4599b2241ebd51e80c89a34a4e4590dc6440b4602d",
				"c69ca3b231f72480e805910a49e4c476bd2d7d0d895db263ca16c4084ea616c2",
				"c92e8ffea5302f0491f648d6cb575a11e02c040bf6d3a03fe4487dd0dd068d8b",
				"c92f140477512b1c8a606844578d0d1667a397d426b63c9c03825d5efdd1a4f7",
				"cdad63c901e1feafd1caf8f2895813d61fdce8b54997ccfe28acce478956ae51",
				"ce4d040688c1a407fc358f5af5a5c65eb40622d1493cc0ea1dd264679e05911e",
				"ce9b9cfa48f87da0550707341f3dc2662ef47fe28c77875826cba3e71c5675bb",
				"d09f9fb7ca971f9541ee56e21252323a1a03d799043230dbaca005d9414e52f5",
				"d21000b8989ab48191dfc428c8ecf594f1ba01ef0fd4f88997f127ac43d693a7",
				"d99b9e2116edfc54f561ba7b6eb28ca561414b6f3d3d0bcf184b9b44fe2b6382",
				"db70274cb346a4c5bb0b954d155604f4aeb077ef60e6e7491cf30a474b46f9cd",
				"e0a518dbd463108d41abbb226784b98c10d2ffca90691203b2ea0bd2744cce75",
				"e163e7fcd0df335082e867fef610e7ecd47b871189522df730393bacc399a9ef",
				"e18135df6f98a34d14193f441b9a55c55fbf2dd1b54eca1b4009ff77d6956ead",
				"e4d252f0cb4fb62963531c3d383da6ad9589443aef21a5e69fb88211e12eed0b",
				"ecf13628c8ef77c5d000a88e2a4888297da2e7d8c39e273fce121ce79e3104a0",
				"f0bb8c0675503e64f2a7dc0355786ea622b67f94c3d705d874397b557b89c439",
				"f718f1375ceeeed1743fafbee1dfcccf38ca2fad7783dae2d8ddead1b8b44a63",
				"fc0dc51cf375fb7692707b6e7d4b9649aa7a4a0024838d3526a807f1224501ba",
				"fc8bca4ba7e9c82f8c06e2894b376ce6f4ebb5873b99149e7ae491bc2bf730df",
				"fef1892566463876586aa05858928f3b830ef30337f5fb9f02c476d426377b16",
				"ff05be18c8ff2640641ff6ce4bc4c6174adc209bcfe15b1200661c0793fd7975",
				"ff7d356be02f5667ef83e5976b13927e567a8ce9d18904855473ca734b884521",
			];
			if(in_array($by_IP, $banned)) {
				$this->last_error = 'You were banned.';
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
