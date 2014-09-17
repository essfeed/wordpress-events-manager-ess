<?php
/**
 * Universal ESS EventFeed Entry Writer
 * FeedValidator class - contain static method to validate specific ESS Fields
 *
 * The feed can be validate in : http://essfeed.org/index.php/ESS:Validator
 *
 *
 * @package 	FeedValidator
 * @author  	Brice Pissard
 * @copyright 	No copyright
 * @license   	GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link		http://essfeed.org/index.php/ESS:Validator
 * @link		https://github.com/essfeed
 */
final class FeedValidator
{
	// Useful DB Structure: ENUM("AU","AF","AL","DZ","AS","AD","AO","AI","AQ","AG","AR","AM","AW","AT","AZ","BS","BH","BD","BB","BY","BE","BZ","BJ","BM","BT","BO","BA","BW","BV","BR","IO","BN","BG","BF", "BI","KH","CM","CA","CV","KY","CF","TD","CL","CN","CX","CC","CO","KM","CG","CD","CK","CR","CI","HR","CU","CY","CZ","DK","DJ","DM","DO","TP","EC","EG","SV","GQ","ER","EE","ET","FK","FO","FJ","FI","FR","FX","GF","PF","TF","GA","GM","GE","DE","GH","GI","GR","GL","GD","GP","GU","GT","GN","GW","GY","HT","HM","HN","HK","HU","IS","IN","ID","IR","IQ","IE","IL","IT","JM","JP","JT","JO","KZ","KE","KI","KP","KR","KW","KG","LA","LV","LB","LS","LR","LY","LI","LT","LU","MO","MK","MG","MW","MY","MV","ML","MT","MH","MQ","MR","MU","YT","MX","FM","MD","MC","MN","MS","MA","MZ","MM","NA","NR","NP","NL","AN","NC","NZ","NI","NE","NG","NU","NF","MP","NO","OM","PK","PW","PS","PA","PG","PY","PE","PH","PN","PL","PT","PR","QA","RE","RO","RU","RW","WS","SH","KN","LC","PM","VC","SM","ST","SA","SN","SC","SL","SG","SK","SI","SB","SO", "ZA","GS","ES","LK","XX","SD","SR","SJ","SZ","SE","CH","SY","TW","TJ","TZ","TH","TL","TG","TK", "TO","TT","TN","TR","TM","TC","TV","UG","UA","AE","GB","UM","US","HV","UY","UZ","VU","VA","VE","VN","VG","VI","WF","EH","YE","YD","YU","ZR","ZM","ZW")
	public static $COUNTRIES_ = array(
	  "AU" => "Australia",
	  "AF" => "Afghanistan",
	  "AL" => "Albania",
	  "DZ" => "Algeria",
	  "AS" => "American Samoa",
	  "AD" => "Andorra",
	  "AO" => "Angola",
	  "AI" => "Anguilla",
	  "AQ" => "Antarctica",
	  "AG" => "Antigua & Barbuda",
	  "AR" => "Argentina",
	  "AM" => "Armenia",
	  "AW" => "Aruba",
	  "AT" => "Austria",
	  "AZ" => "Azerbaijan",
	  "BS" => "Bahamas",
	  "BH" => "Bahrain",
	  "BD" => "Bangladesh",
	  "BB" => "Barbados",
	  "BY" => "Belarus",
	  "BE" => "Belgium",
	  "BZ" => "Belize",
	  "BJ" => "Benin",
	  "BM" => "Bermuda",
	  "BT" => "Bhutan",
	  "BO" => "Bolivia",
	  "BA" => "Bosnia/Hercegovina",
	  "BW" => "Botswana",
	  "BV" => "Bouvet Island",
	  "BR" => "Brazil",
	  "IO" => "British Indian Ocean Territory",
	  "BN" => "Brunei Darussalam",
	  "BG" => "Bulgaria",
	  "BF" => "Burkina Faso",
	  "BI" => "Burundi",
	  "KH" => "Cambodia",
	  "CM" => "Cameroon",
	  "CA" => "Canada",
	  "CV" => "Cape Verde",
	  "KY" => "Cayman Is",
	  "CF" => "Central African Republic",
	  "TD" => "Chad",
	  "CL" => "Chile",
	  "CN" => "China, People's Republic of",
	  "CX" => "Christmas Island",
	  "CC" => "Cocos Islands",
	  "CO" => "Colombia",
	  "KM" => "Comoros",
	  "CG" => "Congo",
	  "CD" => "Congo, Democratic Republic",
	  "CK" => "Cook Islands",
	  "CR" => "Costa Rica",
	  "CI" => "Cote d'Ivoire",
	  "HR" => "Croatia",
	  "CU" => "Cuba",
	  "CY" => "Cyprus",
	  "CZ" => "Czech Republic",
	  "DK" => "Denmark",
	  "DJ" => "Djibouti",
	  "DM" => "Dominica",
	  "DO" => "Dominican Republic",
	  "TP" => "East Timor",
	  "EC" => "Ecuador",
	  "EG" => "Egypt",
	  "SV" => "El Salvador",
	  "GQ" => "Equatorial Guinea",
	  "ER" => "Eritrea",
	  "EE" => "Estonia",
	  "ET" => "Ethiopia",
	  "FK" => "Falkland Islands",
	  "FO" => "Faroe Islands",
	  "FJ" => "Fiji",
	  "FI" => "Finland",
	  "FR" => "France",
	  "FX" => "France, Metropolitan",
	  "GF" => "French Guiana",
	  "PF" => "French Polynesia",
	  "TF" => "French South Territories",
	  "GA" => "Gabon",
	  "GM" => "Gambia",
	  "GE" => "Georgia",
	  "DE" => "Germany",
	  "GH" => "Ghana",
	  "GI" => "Gibraltar",
	  "GR" => "Greece",
	  "GL" => "Greenland",
	  "GD" => "Grenada",
	  "GP" => "Guadeloupe",
	  "GU" => "Guam",
	  "GT" => "Guatemala",
	  "GN" => "Guinea",
	  "GW" => "Guinea-Bissau",
	  "GY" => "Guyana",
	  "HT" => "Haiti",
	  "HM" => "Heard Island And Mcdonald Island",
	  "HN" => "Honduras",
	  "HK" => "Hong Kong",
	  "HU" => "Hungary",
	  "IS" => "Iceland",
	  "IN" => "India",
	  "ID" => "Indonesia",
	  "IR" => "Iran",
	  "IQ" => "Iraq",
	  "IE" => "Ireland",
	  "IL" => "Israel",
	  "IT" => "Italy",
	  "JM" => "Jamaica",
	  "JP" => "Japan",
	  "JT" => "Johnston Island",
	  "JO" => "Jordan",
	  "KZ" => "Kazakhstan",
	  "KE" => "Kenya",
	  "KI" => "Kiribati",
	  "KP" => "Korea, Democratic Peoples Republic",
	  "KR" => "Korea, Republic of",
	  "KW" => "Kuwait",
	  "KG" => "Kyrgyzstan",
	  "LA" => "Lao People's Democratic Republic",
	  "LV" => "Latvia",
	  "LB" => "Lebanon",
	  "LS" => "Lesotho",
	  "LR" => "Liberia",
	  "LY" => "Libyan Arab Jamahiriya",
	  "LI" => "Liechtenstein",
	  "LT" => "Lithuania",
	  "LU" => "Luxembourg",
	  "MO" => "Macau",
	  "MK" => "Macedonia",
	  "MG" => "Madagascar",
	  "MW" => "Malawi",
	  "MY" => "Malaysia",
	  "MV" => "Maldives",
	  "ML" => "Mali",
	  "MT" => "Malta",
	  "MH" => "Marshall Islands",
	  "MQ" => "Martinique",
	  "MR" => "Mauritania",
	  "MU" => "Mauritius",
	  "YT" => "Mayotte",
	  "MX" => "Mexico",
	  "FM" => "Micronesia",
	  "MD" => "Moldavia",
	  "MC" => "Monaco",
	  "MN" => "Mongolia",
	  "MS" => "Montserrat",
	  "MA" => "Morocco",
	  "MZ" => "Mozambique",
	  "MM" => "Union Of Myanmar",
	  "NA" => "Namibia",
	  "NR" => "Nauru Island",
	  "NP" => "Nepal",
	  "NL" => "Netherlands",
	  "AN" => "Netherlands Antilles",
	  "NC" => "New Caledonia",
	  "NZ" => "New Zealand",
	  "NI" => "Nicaragua",
	  "NE" => "Niger",
	  "NG" => "Nigeria",
	  "NU" => "Niue",
	  "NF" => "Norfolk Island",
	  "MP" => "Mariana Islands, Northern",
	  "NO" => "Norway",
	  "OM" => "Oman",
	  "PK" => "Pakistan",
	  "PW" => "Palau Islands",
	  "PS" => "Palestine",
	  "PA" => "Panama",
	  "PG" => "Papua New Guinea",
	  "PY" => "Paraguay",
	  "PE" => "Peru",
	  "PH" => "Philippines",
	  "PN" => "Pitcairn",
	  "PL" => "Poland",
	  "PT" => "Portugal",
	  "PR" => "Puerto Rico",
	  "QA" => "Qatar",
	  "RE" => "Reunion Island",
	  "RO" => "Romania",
	  "RU" => "Russian Federation",
	  "RW" => "Rwanda",
	  "WS" => "Samoa",
	  "SH" => "St Helena",
	  "KN" => "St Kitts & Nevis",
	  "LC" => "St Lucia",
	  "PM" => "St Pierre & Miquelon",
	  "VC" => "St Vincent",
	  "SM" => "San Marino",
	  "ST" => "Sao Tome & Principe",
	  "SA" => "Saudi Arabia",
	  "SN" => "Senegal",
	  "SC" => "Seychelles",
	  "SL" => "Sierra Leone",
	  "SG" => "Singapore",
	  "SK" => "Slovakia",
	  "SI" => "Slovenia",
	  "SB" => "Solomon Islands",
	  "SO" => "Somalia",
	  "ZA" => "South Africa",
	  "GS" => "South Georgia and South Sandwich",
	  "ES" => "Spain",
	  "LK" => "Sri Lanka",
	  "XX" => "Stateless Persons",
	  "SD" => "Sudan",
	  "SR" => "Suriname",
	  "SJ" => "Svalbard and Jan Mayen",
	  "SZ" => "Swaziland",
	  "SE" => "Sweden",
	  "CH" => "Switzerland",
	  "SY" => "Syrian Arab Republic",
	  "TW" => "Taiwan, Republic of China",
	  "TJ" => "Tajikistan",
	  "TZ" => "Tanzania",
	  "TH" => "Thailand",
	  "TL" => "Timor Leste",
	  "TG" => "Togo",
	  "TK" => "Tokelau",
	  "TO" => "Tonga",
	  "TT" => "Trinidad & Tobago",
	  "TN" => "Tunisia",
	  "TR" => "Turkey",
	  "TM" => "Turkmenistan",
	  "TC" => "Turks And Caicos Islands",
	  "TV" => "Tuvalu",
	  "UG" => "Uganda",
	  "UA" => "Ukraine",
	  "AE" => "United Arab Emirates",
	  "GB" => "United Kingdom",
	  "UM" => "US Minor Outlying Islands",
	  "US" => "USA",
	  "HV" => "Upper Volta",
	  "UY" => "Uruguay",
	  "UZ" => "Uzbekistan",
	  "VU" => "Vanuatu",
	  "VA" => "Vatican City State",
	  "VE" => "Venezuela",
	  "VN" => "Vietnam",
	  "VG" => "Virgin Islands (British)",
	  "VI" => "Virgin Islands (US)",
	  "WF" => "Wallis And Futuna Islands",
	  "EH" => "Western Sahara",
	  "YE" => "Yemen Arab Rep.",
	  "YD" => "Yemen Democratic",
	  "YU" => "Yugoslavia",
	  "ZR" => "Zaire",
	  "ZM" => "Zambia",
	  "ZW" => "Zimbabwe"
	);

	// Useful DB Structure: ENUM('aa','ab','ae','af','ak','am','an','ar','as','av','ay','az','ba','be','bg','bh','bi','bm','bn','bo','br','bs','ca','ce','ch','co','cr','cs','cu','cv','cy','da','de','dv','dz','ee','el','en','eo','es','et','eu','fa','ff','fi','fj','fo','fr','fy','ga','gd','gl','gn','gu','gv','ha','he','hi','ho','hr','ht','hu','hy','hz','ia','id','ie','ig','ii','ik','io','is','it','iu','ja','jv','ka','kg','ki','kj','kk','kl','km','kn','ko','kr','ks','ku','kv','kw','ky','la','lb','lg','li','ln','lo','lt','lu','lv','mg','mh','mi','mk','ml','mn','mr','ms','mt','my','na','nb','nd','ne','ng','nl','nn','no','nr','nv','ny','oc','oj','om','or','os','pa','pi','pl','ps','pt','qu','rm','rn','ro','ru','rw','sa','sc','sd','se','sg','si','sk','sl','sm','sn','so','sq','sr','ss','st','su','sv','sw','ta','te','tg','th','ti','tk','tl','tn','to','tr','ts','tt','tw','ty','ug','uk','ur','uz','ve','vi','vo','wa','wo','xh','yi','yo','za','zh','zu')
	public static $LANGUAGES_ = array(
	    'aa' => 'Afar',
	    'ab' => 'Abkhaz',
	    'ae' => 'Avestan',
	    'af' => 'Afrikaans',
	    'ak' => 'Akan',
	    'am' => 'Amharic',
	    'an' => 'Aragonese',
	    'ar' => 'Arabic',
	    'as' => 'Assamese',
	    'av' => 'Avaric',
	    'ay' => 'Aymara',
	    'az' => 'Azerbaijani',
	    'ba' => 'Bashkir',
	    'be' => 'Belarusian',
	    'bg' => 'Bulgarian',
	    'bh' => 'Bihari',
	    'bi' => 'Bislama',
	    'bm' => 'Bambara',
	    'bn' => 'Bengali',
	    'bo' => 'Tibetan Standard, Tibetan, Central',
	    'br' => 'Breton',
	    'bs' => 'Bosnian',
	    'ca' => 'Catalan; Valencian',
	    'ce' => 'Chechen',
	    'ch' => 'Chamorro',
	    'co' => 'Corsican',
	    'cr' => 'Cree',
	    'cs' => 'Czech',
	    'cu' => 'Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic',
	    'cv' => 'Chuvash',
	    'cy' => 'Welsh',
	    'da' => 'Danish',
	    'de' => 'German',
	    'dv' => 'Divehi; Dhivehi; Maldivian;',
	    'dz' => 'Dzongkha',
	    'ee' => 'Ewe',
	    'el' => 'Greek, Modern',
	    'en' => 'English',
	    'eo' => 'Esperanto',
	    'es' => 'Spanish; Castilian',
	    'et' => 'Estonian',
	    'eu' => 'Basque',
	    'fa' => 'Persian',
	    'ff' => 'Fula; Fulah; Pulaar; Pular',
	    'fi' => 'Finnish',
	    'fj' => 'Fijian',
	    'fo' => 'Faroese',
	    'fr' => 'French',
	    'fy' => 'Western Frisian',
	    'ga' => 'Irish',
	    'gd' => 'Scottish Gaelic; Gaelic',
	    'gl' => 'Galician',
	    'gn' => 'GuaranÃ­',
	    'gu' => 'Gujarati',
	    'gv' => 'Manx',
	    'ha' => 'Hausa',
	    'he' => 'Hebrew (modern)',
	    'hi' => 'Hindi',
	    'ho' => 'Hiri Motu',
	    'hr' => 'Croatian',
	    'ht' => 'Haitian; Haitian Creole',
	    'hu' => 'Hungarian',
	    'hy' => 'Armenian',
	    'hz' => 'Herero',
	    'ia' => 'Interlingua',
	    'id' => 'Indonesian',
	    'ie' => 'Interlingue',
	    'ig' => 'Igbo',
	    'ii' => 'Nuosu',
	    'ik' => 'Inupiaq',
	    'io' => 'Ido',
	    'is' => 'Icelandic',
	    'it' => 'Italian',
	    'iu' => 'Inuktitut',
	    'ja' => 'Japanese (ja)',
	    'jv' => 'Javanese (jv)',
	    'ka' => 'Georgian',
	    'kg' => 'Kongo',
	    'ki' => 'Kikuyu, Gikuyu',
	    'kj' => 'Kwanyama, Kuanyama',
	    'kk' => 'Kazakh',
	    'kl' => 'Kalaallisut, Greenlandic',
	    'km' => 'Khmer',
	    'kn' => 'Kannada',
	    'ko' => 'Korean',
	    'kr' => 'Kanuri',
	    'ks' => 'Kashmiri',
	    'ku' => 'Kurdish',
	    'kv' => 'Komi',
	    'kw' => 'Cornish',
	    'ky' => 'Kirghiz, Kyrgyz',
	    'la' => 'Latin',
	    'lb' => 'Luxembourgish, Letzeburgesch',
	    'lg' => 'Luganda',
	    'li' => 'Limburgish, Limburgan, Limburger',
	    'ln' => 'Lingala',
	    'lo' => 'Lao',
	    'lt' => 'Lithuanian',
	    'lu' => 'Luba-Katanga',
	    'lv' => 'Latvian',
	    'mg' => 'Malagasy',
	    'mh' => 'Marshallese',
	    'mi' => 'Maori',
	    'mk' => 'Macedonian',
	    'ml' => 'Malayalam',
	    'mn' => 'Mongolian',
	    'mr' => 'Marathi (Mara?hi)',
	    'ms' => 'Malay',
	    'mt' => 'Maltese',
	    'my' => 'Burmese',
	    'na' => 'Nauru',
	    'nb' => 'Norwegian BokmÃ¥l',
	    'nd' => 'North Ndebele',
	    'ne' => 'Nepali',
	    'ng' => 'Ndonga',
	    'nl' => 'Dutch',
	    'nn' => 'Norwegian Nynorsk',
	    'no' => 'Norwegian',
	    'nr' => 'South Ndebele',
	    'nv' => 'Navajo, Navaho',
	    'ny' => 'Chichewa; Chewa; Nyanja',
	    'oc' => 'Occitan',
	    'oj' => 'Ojibwe, Ojibwa',
	    'om' => 'Oromo',
	    'or' => 'Oriya',
	    'os' => 'Ossetian, Ossetic',
	    'pa' => 'Panjabi, Punjabi',
	    'pi' => 'Pali',
	    'pl' => 'Polish',
	    'ps' => 'Pashto, Pushto',
	    'pt' => 'Portuguese',
	    'qu' => 'Quechua',
	    'rm' => 'Romansh',
	    'rn' => 'Kirundi',
	    'ro' => 'Romanian, Moldavian, Moldovan',
	    'ru' => 'Russian',
	    'rw' => 'Kinyarwanda',
	    'sa' => 'Sanskrit (Sa?sk?ta)',
	    'sc' => 'Sardinian',
	    'sd' => 'Sindhi',
	    'se' => 'Northern Sami',
	    'sg' => 'Sango',
	    'si' => 'Sinhala, Sinhalese',
	    'sk' => 'Slovak',
	    'sl' => 'Slovene',
	    'sm' => 'Samoan',
	    'sn' => 'Shona',
	    'so' => 'Somali',
	    'sq' => 'Albanian',
	    'sr' => 'Serbian',
	    'ss' => 'Swati',
	    'st' => 'Southern Sotho',
	    'su' => 'Sundanese',
	    'sv' => 'Swedish',
	    'sw' => 'Swahili',
	    'ta' => 'Tamil',
	    'te' => 'Telugu',
	    'tg' => 'Tajik',
	    'th' => 'Thai',
	    'ti' => 'Tigrinya',
	    'tk' => 'Turkmen',
	    'tl' => 'Tagalog',
	    'tn' => 'Tswana',
	    'to' => 'Tonga (Tonga Islands)',
	    'tr' => 'Turkish',
	    'ts' => 'Tsonga',
	    'tt' => 'Tatar',
	    'tw' => 'Twi',
	    'ty' => 'Tahitian',
	    'ug' => 'Uighur, Uyghur',
	    'uk' => 'Ukrainian',
	    'ur' => 'Urdu',
	    'uz' => 'Uzbek',
	    've' => 'Venda',
	    'vi' => 'Vietnamese',
	    'vo' => 'VolapÃ¼k',
	    'wa' => 'Walloon',
	    'wo' => 'Wolof',
	    'xh' => 'Xhosa',
	    'yi' => 'Yiddish',
	    'yo' => 'Yoruba',
	    'za' => 'Zhuang, Chuang',
	    'zh' => 'Chinese',
	    'zu' => 'Zulu',
	);

	// Useful DB Structure: ENUM('AFA','ALL','DZD','USD','EUR','AOA','XCD','NOK','XCD','ARA','AMD','AWG','AUD','AZM','BSD','BHD','BDT','BBD','BYR','BZD','XAF','BMD','BTN','BOB','BAM','BWP','BRL','GBP','BND','BGN','XAF','BIF','KHR','XAF','CAD','CVE','KYD','XAF','XAF','CLF','CNY','COP','KMF','CDZ','XAF','NZD','CRC','HRK','CUP','CZK','DKK','DJF','XCD','DOP','TPE','EGP','XAF','ERN','EEK','ETB','FKP','DKK','FJD','XPF','XAF','GMD','GEL','GHC','GIP','DKK','XCD','GTQ','GNS','GWP','GYD','HTG','HNL','HKD','HUF','ISK','INR','IDR','IRR','IQD','ILS','XAF','JMD','JPY','JOD','KZT','KES','KPW','KRW','KWD','KGS','LAK','LVL','LBP','LSL','LRD','LYD','CHF','LTL','MOP','MKD','MGF','MWK','MYR','MVR','XAF','MRO','MUR','MXN','MDL','MNT','XCD','MAD','MZM','MMK','NAD','NPR','ANG','XPF','NZD','NIC','XOF','NGN','NZD','OMR','PKR','PAB','PGK','PYG','PEI','PHP','NZD','PLN','QAR','ROL','RUB','RWF','XCD','XCD','XCD','WST','STD','SAR','XOF','SCR','SLL','SGD','SBD','SOS','ZAR','LKR','SHP','SDG','SRG','SZL','SEK','CHF','SYP','TWD','TJR','TZS','THB','XAF','NZD','TOP','TTD','TND','TRY','TMM','UGS','UAH','SUR','AED','UYU','UZS','VUV','VEF','VND','XPF','XOF','MAD','ZMK')
	public static $CURRENCIES_ = array(
		'AF' => 'AFA',
		'AL' => 'ALL',
		'DZ' => 'DZD',
		'AS' => 'USD',
		'AD' => 'EUR',
		'AO' => 'AOA',
		'AI' => 'XCD',
		'AQ' => 'NOK',
		'AG' => 'XCD',
		'AR' => 'ARA',
		'AM' => 'AMD',
		'AW' => 'AWG',
		'AU' => 'AUD',
		'AT' => 'EUR',
		'AZ' => 'AZM',
		'BS' => 'BSD',
		'BH' => 'BHD',
		'BD' => 'BDT',
		'BB' => 'BBD',
		'BY' => 'BYR',
		'BE' => 'EUR',
		'BZ' => 'BZD',
		'BJ' => 'XAF',
		'BM' => 'BMD',
		'BT' => 'BTN',
		'BO' => 'BOB',
		'BA' => 'BAM',
		'BW' => 'BWP',
		'BV' => 'NOK',
		'BR' => 'BRL',
		'IO' => 'GBP',
		'BN' => 'BND',
		'BG' => 'BGN',
		'BF' => 'XAF',
		'BI' => 'BIF',
		'KH' => 'KHR',
		'CM' => 'XAF',
		'CA' => 'CAD',
		'CV' => 'CVE',
		'KY' => 'KYD',
		'CF' => 'XAF',
		'TD' => 'XAF',
		'CL' => 'CLF',
		'CN' => 'CNY',
		'CX' => 'AUD',
		'CC' => 'AUD',
		'CO' => 'COP',
		'KM' => 'KMF',
		'CD' => 'CDZ',
		'CG' => 'XAF',
		'CK' => 'NZD',
		'CR' => 'CRC',
		'HR' => 'HRK',
		'CU' => 'CUP',
		'CY' => 'EUR',
		'CZ' => 'CZK',
		'DK' => 'DKK',
		'DJ' => 'DJF',
		'DM' => 'XCD',
		'DO' => 'DOP',
		'TP' => 'TPE',
		'EC' => 'USD',
		'EG' => 'EGP',
		'SV' => 'USD',
		'GQ' => 'XAF',
		'ER' => 'ERN',
		'EE' => 'EEK',
		'ET' => 'ETB',
		'FK' => 'FKP',
		'FO' => 'DKK',
		'FJ' => 'FJD',
		'FI' => 'EUR',
		'FR' => 'EUR',
		'FX' => 'EUR',
		'GF' => 'EUR',
		'PF' => 'XPF',
		'TF' => 'EUR',
		'GA' => 'XAF',
		'GM' => 'GMD',
		'GE' => 'GEL',
		'DE' => 'EUR',
		'GH' => 'GHC',
		'GI' => 'GIP',
		'GR' => 'EUR',
		'GL' => 'DKK',
		'GD' => 'XCD',
		'GP' => 'EUR',
		'GU' => 'USD',
		'GT' => 'GTQ',
		'GN' => 'GNS',
		'GW' => 'GWP',
		'GY' => 'GYD',
		'HT' => 'HTG',
		'HM' => 'AUD',
		'VA' => 'EUR',
		'HN' => 'HNL',
		'HK' => 'HKD',
		'HU' => 'HUF',
		'IS' => 'ISK',
		'IN' => 'INR',
		'ID' => 'IDR',
		'IR' => 'IRR',
		'IQ' => 'IQD',
		'IE' => 'EUR',
		'IL' => 'ILS',
		'IT' => 'EUR',
		'CI' => 'XAF',
		'JM' => 'JMD',
		'JP' => 'JPY',
		'JO' => 'JOD',
		'KZ' => 'KZT',
		'KE' => 'KES',
		'KI' => 'AUD',
		'KP' => 'KPW',
		'KR' => 'KRW',
		'KW' => 'KWD',
		'KG' => 'KGS',
		'LA' => 'LAK',
		'LV' => 'LVL',
		'LB' => 'LBP',
		'LS' => 'LSL',
		'LR' => 'LRD',
		'LY' => 'LYD',
		'LI' => 'CHF',
		'LT' => 'LTL',
		'LU' => 'EUR',
		'MO' => 'MOP',
		'MK' => 'MKD',
		'MG' => 'MGF',
		'MW' => 'MWK',
		'MY' => 'MYR',
		'MV' => 'MVR',
		'ML' => 'XAF',
		'MT' => 'EUR',
		'MH' => 'USD',
		'MQ' => 'EUR',
		'MR' => 'MRO',
		'MU' => 'MUR',
		'YT' => 'EUR',
		'MX' => 'MXN',
		'FM' => 'USD',
		'MD' => 'MDL',
		'MC' => 'EUR',
		'MN' => 'MNT',
		'MS' => 'XCD',
		'MA' => 'MAD',
		'MZ' => 'MZM',
		'MM' => 'MMK',
		'NA' => 'NAD',
		'NR' => 'AUD',
		'NP' => 'NPR',
		'NL' => 'EUR',
		'AN' => 'ANG',
		'NC' => 'XPF',
		'NZ' => 'NZD',
		'NI' => 'NIC',
		'NE' => 'XOF',
		'NG' => 'NGN',
		'NU' => 'NZD',
		'NF' => 'AUD',
		'MP' => 'USD',
		'NO' => 'NOK',
		'OM' => 'OMR',
		'PK' => 'PKR',
		'PW' => 'USD',
		'PA' => 'PAB',
		'PG' => 'PGK',
		'PY' => 'PYG',
		'PE' => 'PEI',
		'PH' => 'PHP',
		'PN' => 'NZD',
		'PL' => 'PLN',
		'PT' => 'EUR',
		'PR' => 'USD',
		'QA' => 'QAR',
		'RE' => 'EUR',
		'RO' => 'ROL',
		'RU' => 'RUB',
		'RW' => 'RWF',
		'KN' => 'XCD',
		'LC' => 'XCD',
		'VC' => 'XCD',
		'WS' => 'WST',
		'SM' => 'EUR',
		'ST' => 'STD',
		'SA' => 'SAR',
		'SN' => 'XOF',
		'CS' => 'EUR',
		'SC' => 'SCR',
		'SL' => 'SLL',
		'SG' => 'SGD',
		'SK' => 'EUR',
		'SI' => 'EUR',
		'SB' => 'SBD',
		'SO' => 'SOS',
		'ZA' => 'ZAR',
		'GS' => 'GBP',
		'ES' => 'EUR',
		'LK' => 'LKR',
		'SH' => 'SHP',
		'PM' => 'EUR',
		'SD' => 'SDG',
		'SR' => 'SRG',
		'SJ' => 'NOK',
		'SZ' => 'SZL',
		'SE' => 'SEK',
		'CH' => 'CHF',
		'SY' => 'SYP',
		'TW' => 'TWD',
		'TJ' => 'TJR',
		'TZ' => 'TZS',
		'TH' => 'THB',
		'TG' => 'XAF',
		'TK' => 'NZD',
		'TO' => 'TOP',
		'TT' => 'TTD',
		'TN' => 'TND',
		'TR' => 'TRY',
		'TM' => 'TMM',
		'TC' => 'USD',
		'TV' => 'AUD',
		'UG' => 'UGS',
		'UA' => 'UAH',
		'SU' => 'SUR',
		'AE' => 'AED',
		'GB' => 'GBP',
		'US' => 'USD',
		'UM' => 'USD',
		'UY' => 'UYU',
		'UZ' => 'UZS',
		'VU' => 'VUV',
		'VE' => 'VEF',
		'VN' => 'VND',
		'VG' => 'USD',
		'VI' => 'USD',
		'WF' => 'XPF',
		'XO' => 'XOF',
		'EH' => 'MAD',
		'ZM' => 'ZMK',
		'ZW' => 'USD'
	);

	function __construct(){}

	/**
	 * Check is the content to evaluate is empty
	 *
	 * @access	public
	 * @param	String	Object to evaluate ()
	 * @return	Boolean
	 */
	public static function isNull( $obj=null )
	{
		$objS = trim( str_replace( array( '	', ' ' ), '', self::removeBreaklines( $obj ) ) );

		return ( $objS == '' || $objS == null ||
			( !is_string( $obj ) && intval( $obj ) <= 0 ) ||
			( is_numeric( $obj ) && intval( $obj ) <= 0 ) ||
			( is_bool( $obj ) && $obj == FALSE )
		)? TRUE : FALSE;
	}

	/**
	 * Control the correct syntax of the date in UTC format (ISO 8601)
	 * to check if the string formated UTC date is valid (e.g. 2013-10-31T15:30:59Z)
	 *
	 * @access	public
	 * @param	String	stringDate date string content format ISO 8601 (e.g. 2013-10-31T15:30:59Z)
	 * @return	Boolean
	 */
	public static function isValidDate( $stringDate='' )
	{
		if ( self::isNull( $stringDate ) ) return FALSE;

		$matcher = preg_match( "/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((\+|-)\d{2}(:?\d{2})?))?$/", str_replace( ' ', 'T', $stringDate ) );
		$t_sep 	 = explode( 'T', strtoupper( $stringDate ) );

		if ( @count( $t_sep ) > 1 )
		{
			$time_sep = explode( ':', $t_sep[ 1 ] );

			if ( intval( $time_sep[ 0 ] ) > 24 )
				return FALSE;

			if ( @count( $time_sep ) <= 4 )
			{
				for ( $i=1 ; $i<@count( $time_sep ) ; $i++ )
				{
					if ( intval( $time_sep[ $i ] ) > 59 && $i < 3 )
						return FALSE;
				}
			}
		}

		//var_dump( $stringDate, $matcher);

		if ( $matcher == 1 )
		{
			try
			{
				$err = new DateTime( $stringDate, new DateTimeZone( 'GMT' ) );
				return TRUE;
			}
			catch( Exception $e )
			{
				return FALSE;
			}
		}

		return FALSE;
	}

	/**
	 * Control if the URL is correctly formated (RFC 3986)
	 * An IP can also be submited as a URL.
	 *
	 * @access	public
	 * @param	String	stringDate string element to control
	 * @return	Boolean
	 */
	public static function isValidURL( $url='' )
	{
		$url = trim( $url );
		$ereg = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";

		return ( preg_match( $ereg, $url ) > 0 && strlen( $url ) > 10 )? TRUE : self::isValidIP( $url );
	}

	/**
	 * Control if the URL is contain a valid media file
	 *
	 * @access	public
	 * @param	String	string Media URL to control
	 * @return	Boolean
	 */
	public static function isValidMediaURL( $url, $type='image' )
	{
		$url = trim( $url );

		switch( strtolower( $type ) )
		{
			default :
			case 'image' : $MEDIA_FORMAT = array('ART','AVI','AVS','BMP','CUR','EPS','GIF','ICO','JPG','JPEG','PDF','PIX','PNG','PSD','RGB','SVG','TGA','TIF','TIM','TTF','TXT','WMF','WPG','TIF','MPG'); break;
			case 'video' : $MEDIA_FORMAT = array('FLV','MPG','AVI','MOV','ACC','AAC','MP4','3GP','OGG','FLA','M4V','WMV','DAT','NSV'); break;
			case 'sound' : $MEDIA_FORMAT = array('M4A','MP3','M4P','MPC','OGG','AMR','GSM','WAV','WMA','VOX','RAW','MPC'); break;
		}

		$ex_ = explode( '.', $url );

		return ( strlen( $url ) > 0 &&
			in_array( strtoupper( substr( $ex_[ count( $ex_ )-1 ],0, strlen( $ex_[ count( $ex_ )-1 ] ) ) ), $MEDIA_FORMAT )
		)? TRUE : FALSE;
	}

	/**
	 * Get the media file type.
	 *
	 * @access	public
	 * @param	String	String Media URL to check
	 * @return	String	Return the media type: 'image', 'video', 'sound' or 'website' or NULL if not found
	 */
	public static function getMediaType( $url )
	{
		if ( strlen( trim( $url ) ) > 0 )
		{
			$MEDIA_IMAGE = array('ART','AVI','AVS','BMP','CUR','EPS','GIF','ICO','JPG','PDF','PIX','PNG','PSD','RGB','SVG','TGA','TIF','TIM','TTF','TXT','WMF','WPG','TIF','MPG');
			$MEDIA_SOUND = array('M4A','MP3','M4P','MPC','OGG','AMR','GSM','WAV','WMA','VOX','RAW','MPC');
			$MEDIA_VIDEO = array('FLV','MPG','AVI','MOV','ACC','AAC','MP4','3GP','OGG','FLA','M4V','WMV','DAT','NSV');

			$ex_ = explode( '.', $url );

			// detect some specific website URL video content
			$VIDEO_WEBSITES = array(
				'youtube.com',
				'vimeo.com',
				'ted.com',
				'dailymotion.com',
				'current.com',
				'bigthink.com',
				'atom.com',
				'blip.tv',
				'5min.com',
				'hulu.com',
				'stickam.com',
				'ustream.tv',
				'blinkx.com',
				'wimp.com'
			);

			$domain = parse_url( $url );
			$dh_ = explode( '.', $domain['host'] );
			if ( count( $dh_ ) > 1 ) { $domain['host'] = $dh_[ count( $dh_ )-2].".".$dh_[ count( $dh_ )-1]; } // remove www.

			if ( in_array( strtolower( $domain['host'] ), $VIDEO_WEBSITES ) ) 					 { return 'video'; }
			if ( in_array( strtoupper( substr( $ex_[ count( $ex_ )-1 ],0,3) ), $MEDIA_IMAGE ) ) { return 'image'; }
			if ( in_array( strtoupper( substr( $ex_[ count( $ex_ )-1 ],0,3) ), $MEDIA_VIDEO ) ) { return 'video'; }
			if ( in_array( strtoupper( substr( $ex_[ count( $ex_ )-1 ],0,3) ), $MEDIA_SOUND ) ) { return 'sound'; }
		}
		return ( self::isValidURL( $url ) )? 'website' : NULL;
	}

	/**
	 * 	Control if the parameter submited is a valide IP v4
	 *
	 * 	@access public
	 * 	@param	String	Value of the IP to evaluate
	 * 	@return	Boolean	If the parameter submited is a valide IP return TRUE, FALSE else.
	 */
	public static function isValidIP( $ip='' )
	{
		$ip = trim( $ip );
		$regexp = '/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])$/';

		if ( preg_match( $regexp, $ip ) <= 0 )
		{
			return FALSE;
		}
		else
		{
			$a = explode( ".", $ip );

			if ( $a[0] > 255) { return FALSE; }
			if ( $a[1] > 255) { return FALSE; }
			if ( $a[2] > 255) {	return FALSE; }
			if ( $a[3] > 255) { return FALSE; }

			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Control if the Email submited is correctly formated (RFC 5321)
	 *
	 * @access	public
	 * @param	String	stringDate string element to control
	 * @return	Boolean
	 */
	public static function isValidEmail( $email )
	{
		$email = trim( $email );
		if ( self::isNull( $email ) ) return FALSE;

		if ( preg_match( '/^\w[-.\w]*@(\w[-._\w]*\.[a-zA-Z]{2,}.*)$/', $email, $matches ) )
        {
        	$hostName = $matches[ 1 ];

			if ( strlen( $hostName ) > 5 )
			{
	         	if ( function_exists('checkdnsrr') )
				{
					if ( checkdnsrr( $hostName . '.', 'MX' ) ) return TRUE;
					if ( checkdnsrr( $hostName . '.', 'A'  ) ) return TRUE;
				}
				else
				{
					exec( "nslookup -type=MX ".$hostName, $r );

					if ( count( $r ) > 0 )
					{
						foreach ( $r as $line )
						{
							if ( preg_match( "^$hostName", $line ) ) return TRUE;
						}
						return FALSE;
					}
					else return TRUE; // if a problem occured while resolving the MX consider the email as valid
				}
			}
        }
		else
		{
			if ( preg_match( "^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,3}$", $email ) > 0 )
				return TRUE;
		}
		return FALSE;
	}

	/**
	 * Control if the Country Code submited is correctly formated (ISO 3166-1)
	 * It Must be a 2 chars Country Code (US, FR, ES)
	 *
	 * @access	public
	 * @param	String	stringDate string element to control
	 * @return	Boolean
	 */
	public static function isValidCountryCode( $countryCode )
	{
		$countryCode = strtoupper( trim( $countryCode ) );

		if ( self::isNull( $countryCode ) ) return FALSE;

		foreach ( self::$COUNTRIES_ as $countryC => $countryN )
		{
			if ( $countryCode == $countryC ) return TRUE;
		}
		return FALSE;
	}

	/**
	 * Control if the Language Code submited is correctly formated (ISO 4217)
	 * It Must be a 2 chars Language Code (EN, FR, ES,..)
	 *
	 * @access	public
	 * @param	String	stringDate string element to control
	 * @return	Boolean
	 */
	public static  function isValidLanguageCode( $languageCode )
	{
		$languageCode = strtolower( $languageCode );

		if ( self::isNull( $languageCode ) ) return FALSE;

		foreach ( self::$LANGUAGES_ as $langC => $langN )
		{
			if ( $languageCode == $langC ) return TRUE;
		}
		return FALSE;
	}

	/**
	 * 	Check if the parameter is a valid decimal Latitude
	 * 	Latitude decimal degrees: -90.XXXXXX to 90.XXXXXX (ISO 6709)
	 *
	 * 	@access	public
	 * 	@param	Float	Value of the Latitude to evaluate.
	 * 	@return Boolean	Return TRUE is the Latitude is valide, FALSE else.
	 */
	public static function isValidLatitude( $latitude=NULL )
	{
		if ( is_float( $latitude ) )
			$latitude = floor( $latitude * 10000000 ) / 10000000;
		else
			$latitude = floor( floatval( $latitude ) * 10000000 ) / 10000000;

		$match_latitude = preg_match( "/^-?([0-8]?[0-9]|90)\.[0-9]{1,7}$/", $latitude );

		return ( $match_latitude == 1 )? TRUE : FALSE;
	}

	/**
	 * 	Check if the parameter is a valid decimal Longitude
	 * 	Longitude decimal degrees: -180.XXXXXX to 180.XXXXXX (ISO 6709)
	 *
	 * 	@access	public
	 * 	@param	Float	Value of the Longitude to evaluate.
	 * 	@return Boolean	Return TRUE is the Longitude is valide, FALSE else.
	 */
	public static function isValidLongitude( $longitude=NULL )
	{
		if ( is_float( $longitude ) )
			$longitude = floor( $longitude * 10000000 ) / 10000000;
		else
			$longitude = floor( floatval( $longitude ) * 10000000 ) / 10000000;

		$match_longitude = preg_match( "/^-?((1?[0-7]?|[0-9]?)[0-9]|180)\.[0-9]{1,7}$/", $longitude );

		return ( $match_longitude == 1 )? TRUE : FALSE;
	}

	/**
	 * 	Check if the parameter contain both numbers and alpha characters.
	 *
	 * 	@access	public
	 * 	@param	Object	Value of the String to evaluate.
	 * 	@return Boolean	Return TRUE is the both elements are found, FALSE else.
	 */
	public static function isAlphaNumChars( $in )
	{
		return ( ( preg_match( "#(*UTF8)[[:alnum:]]#", $in ) > 0 )? TRUE : FALSE );
	}

	/**
	 * 	Check if the parameter submited contain only alpha characters.
	 *
	 * 	@access	public
	 * 	@param	Object	Value of the String to evaluate.
	 * 	@return Boolean	Return TRUE is only alpha chars are found, FALSE else.
	 */
	public static function isOnlyAlphaChars( $in )
	{
		return ( ( preg_match( "#(*UTF8)[[:alpha:]]#", $in ) > 0 )? TRUE : FALSE );
	}

	/**
	 * 	Check if the parameter submited contain only numbers.
	 *
	 * 	@access	public
	 * 	@param	Object	Value of the String to evaluate.
	 * 	@return Boolean	Return TRUE is only numbers are found, FALSE else.
	 */
	public static function isOnlyNumsChars( $in )
	{
		return ( ( preg_match( "/^[0-9]*$/", $in ) > 0 )? TRUE : FALSE );
	}

	/**
	 * 	Check if the parameter submited is a valide 3 chars currency
	 * 	Conform to the standard ISO
	 *
	 * 	@access	public
	 * 	@param	String	Value of the 3 chars currency to evaluate.
	 * 	@return Boolean	Return TRUE is the currency is valide, FALSE else.
	 */
	public static function isValidCurrency( $currency )
	{
		$currency = strtoupper( $currency );

		foreach( self::$CURRENCIES_ as $country => $cur )
		{
			if ( $currency == $cur ) return TRUE;
		}
		return FALSE;
	}

	/**
	 * 	Get a simplification of the text: reformat it to the define Charset (UTF-8 by default)
	 * 	And remove HTML tags and breaklines.
	 *
	 * 	@access	public
	 * 	@param	String	String text to reformat.
	 * 	@param	String	[OPTIONAL] Charset to reset the text
	 * 	@return String	Return reformated text.
	 */
	public static function getOnlyText( $text='', $charset='UTF-8' )
	{
		return self::removeBreaklines(
			self::removeSpecialChars(
				strip_tags(
					self::charsetString( $text, $charset )
				)
			)
		);
	}

	/**
	 * 	Remove accents from the text submited according to a specific Charset (Default UTF-8)
	 *
	 * 	@access	public
	 * 	@param	String	Text from which the accent have to be substitute by the ASCI equivalent.
	 * 	@param 	String	Charset to from whitch the submited text have been encoded.
	 * 	@return String	Text without accent.
	 */
	public static function noAccent( $text='', $charset='UTF-8' )
	{
		return ( ( self::utf8_is_ascii( $text ) == TRUE )?
			self::utf8_accents_to_ascii( self::getOnlyText( $text ) )
			:
			self::utf8_strip_ascii_ctrl( self::getOnlyText( $text ) )
		);
	}

	/**
	 * 	Remove somme HTML elements that can distube the event content broadcast.
	 *
	 * 	@access	public
	 * 	@param	String	String HTML text to reformat.
	 * 	@return String	Return reformated text.
	 */
	public static function stripSpecificHTMLtags( $text='' )
	{
		return preg_replace(
			array(
				/*
				 * Leave/Remove Flash Objects
				'@<embed[^>]*?>.*?</embed>@si',
				'@<param[^>]*?>.*?</param>@si',
				'@<object[^>]*?>.*?</object>@si',
				*
				* Leave/Remove HTML5 Objects
				'@<canvas[^>]*?>.*?</canvas>@si',
				'@<source[^>]*?>.*?</source>@si',
				*/
				'@<noscript[^>]*?>.*?</noscript>@si',
				'@<iframe[^>]*?>.*?</iframe>@si',
				'@<script[^>]*?>.*?</script>@si',
				'@<style[^>]*?>.*?</style>@siU'
				// , '@<![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments including CDATA
			),
			'',
			// Remove extra HTML whitespaces
			preg_replace( '~>\s+<~', '><',
				$text
			)
		);
	}

	/**
	 * 	Reformat a text according to a specific Charset (by default UTF-8).
	 * 	If the Charset is not detected, the text is forwarded with basic reformating
	 * 	@see	self::simplifyText()
	 *
	 * 	@access	public
	 * 	@param	String	String HTML text to reformat.
	 * 	@return String	Return reformated text.
	 */
	public static function charsetString( $text, $charset='UTF-8' )
	{
		if ( strlen( $text ) > 0 )
		{
			$text = trim( self::simplifyText( $text ) );

			if ( function_exists( 'htmlspecialchars' ) )
			{
				$textORG = $text;
				$text = trim(
					htmlspecialchars(
						$text,
						( !defined( 'ENT_DISALLOWED' )? ENT_IGNORE : ENT_DISALLOWED ),
						$charset
					)
				);

				if ( strlen( $text ) <= 0 )
					$text = $textORG;
			}

			$textORG = $text;
			$text = trim( self::unhtmlentities( $text ) );

			if ( strlen( $text ) <= 0 )
				$text = $textORG;

			if ( function_exists( 'mb_convert_encoding' ) )
			{
				$textORG = $text;
				$text = trim( mb_convert_encoding( $text, $charset, "auto" ) );

				if ( strlen( $text ) <= 0 )
					$text = $textORG;
			}

			$textORG = $text;
			$text = trim( self::resolveUnicode( $text ) );

			if ( strlen( $text ) <= 0 )
				$text = $textORG;
		}

		$text_charset_detected = trim( $text );

		return ( strlen( $text_charset_detected ) > 0 )?
			$text_charset_detected
			:
			self::resolveUnicode( self::simplifyText( $text )
		);
	}

	/**
	 * 	Replace anykind of breaklines from a text: HTML, url encoded or graphic breaklines
	 * 	with aspecific character send as segond parameter.
	 *
	 * 	@access	public
	 * 	@param	String	String HTML text to reformat.
	 * 	@param 	String	String to substitute from the breakline found.
	 * 	@return String	Return reformated text.
	 */
	public static function removeBreaklines( $text='', $replace=' ' )
	{
		return preg_replace(
			array(
				'@<br \/>@si',
				'@<br/>@si',
				'@<br>@si',
				'@&lt;br&gt;@si',
				'@&lt;br\/&gt;@si',
				'@&lt;br\ \/&gt;@si',
				'@\n@si',
				'@\r@si'
			),
			$replace,
			$text
		);
	}

	/**
	 * 	Get the Date difference between two dates
	 * 	@see http://www.addedbytes.com/blog/code/php-datediff-function/
	 *
	 * 	@access public
	 * 	@param	String interval type can be:
	 *   				yyyy   Number of full years
	 *   				q      Number of full quarters
	 *   				m      Number of full months
	 *   				y      Difference between day numbers
	 *       				   (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
	 *   				d      Number of full days
	 *   				w      Number of full weekdays
	 *   				ww     Number of full weeks
	 *   				h      Number of full hours
	 *   				n      Number of full minutes
	 *   				s      Number of full seconds (default)
	 * 	@param	int or String	Date from in timestamp format.
	 * 	@param	int	or String 	Date to in timestamp format.
	 * 	@return	int	number of interval type that separate the two dates.
	 */
	public static function getDateDiff( $interval_type='d', $datefrom=null, $dateto=null )
	{
		$datefrom 	= ( ( is_string( $datefrom 	) )? strtotime( $datefrom,  0 ) : floatval( $datefrom ) );
	    $dateto 	= ( ( is_string( $dateto	) )? strtotime( $dateto, 	0 ) : floatval( $dateto   ) );

	    $difference = $dateto - $datefrom; // Difference in seconds

	    switch( $interval_type )
	    {
	    	case 'yyyy': // Number of full years
		        $years_difference = floor( $difference / 31536000 );
	        	if ( mktime(
	        			date("H", $datefrom),
	        			date("i", $datefrom),
	        			date("s", $datefrom),
	        			date("n", $datefrom),
	        			date("j", $datefrom),
	        			date("Y", $datefrom)+$years_difference
					) > $dateto
				)
				{
	            	$years_difference--;
	        	}
	        	if ( mktime(
	        			date("H", $dateto),
	        			date("i", $dateto),
	        			date("s", $dateto),
	        			date("n", $dateto),
	        			date("j", $dateto),
	        			date("Y", $dateto)-( $years_difference+1 )
					) > $datefrom
				)
				{
	            	$years_difference++;
	        	}

	        	$datediff = $years_difference;
	        	break;

	    case "q": // Number of full quarters
	        $quarters_difference = floor($difference / 8035200);

	        while (mktime(
		        	date("H", $datefrom),
		        	date("i", $datefrom),
		        	date("s", $datefrom),
		        	date("n", $datefrom)+($quarters_difference*3),
		        	date("j", $dateto),
		        	date("Y", $datefrom)
				) < $dateto
			)
			{
	            $months_difference++;
	        }
	        $quarters_difference--;
	        $datediff = $quarters_difference;
	        break;

	    case "m": // Number of full months
	        $months_difference = floor( $difference / 2678400 );
	        while (mktime(
		        	date("H", $datefrom),
		        	date("i", $datefrom),
		        	date("s", $datefrom),
		        	date("n", $datefrom)+($months_difference),
		        	date("j", $dateto),
		        	date("Y", $datefrom)
				) < $dateto
			)
			{
	            $months_difference++;
	        }
	        $months_difference--;
	        $datediff = $months_difference;
	        break;

	    case 'y': // Difference between day numbers
	        $datediff = date("z", $dateto) - date("z", $datefrom);
	        break;

	    case "d": // Number of full days
	        $datediff = floor($difference / 86400);
	        break;

	    case "w": // Number of full weekdays
	        $days_difference 	= floor($difference / 86400);
	        $weeks_difference 	= floor($days_difference / 7); // Complete weeks
	        $first_day 			= date("w", $datefrom);
	        $days_remainder 	= floor($days_difference % 7);
	        $odd_days 			= $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
	        if ($odd_days > 7) { // Sunday
	            $days_remainder--;
	        }
	        if ($odd_days > 6) { // Saturday
	            $days_remainder--;
	        }
	        $datediff = ($weeks_difference * 5) + $days_remainder;
	        break;

	    case "ww": // Number of full weeks
	        $datediff = floor($difference / 604800);
	        break;

	    case "h": // Number of full hours OR decimal if < 1 hour
	    	$diff_h = $difference / 3600;
	    	$datediff = ( $diff_h > 0 && $diff_h < 1 )? ( round( $diff_h * 100 ) / 100 ) : floor( $diff_h );
	        break;

	    case "n": // Number of full minutes OR decimal if < 1 minute
	    	$diff_m = $difference / 60;
			$datediff = ( $diff_m > 0 && $diff_m < 1 )? ( round( $diff_m * 100 ) / 100 ) : floor( $diff_m );
	        break;

	    default: // Number of full seconds (default)
	        $datediff = $difference;
	        break;
	    }

	    return $datediff;
	}





	//--------------------------------------------------------------------
	// -- Private Static Methods --
	//--------------------------------------------------------------------




	/**
	 * 	Tests whether a string contains only 7bit ASCII bytes.
	 * 	You might use this to conditionally check whether a string
	 * 	needs handling as UTF-8 or not, potentially offering performance
	 * 	benefits by using the native PHP equivalent if it's just ASCII e.g.;
	 *
	 * 	<code>
	 * 		if ( utf8_is_ascii( $someString ) )
	 * 		{
	 *     		// It's just ASCII - use the native PHP version
	 *     		$someString = strtolower($someString);
	 * 		}
	 * 		else
	 * 		{
	 *     		$someString = utf8_strtolower($someString);
	 * 		}
	 * 	</code>
	 *
	 * 	@param 	String
	 * 	@return Boolean TRUE if it's all ASCII
	 */
	private static function utf8_is_ascii( $str='' )
	{
	    // Search for any bytes which are outside the ASCII range...
	    return ( preg_match('/(?:[^\x00-\x7F])/', $str ) !== 1 );
	}

	/**
	 * 	Strip out all non-7bit ASCII bytes
	 * 	If you need to transmit a string to system which you know can only
	 * 	support 7bit ASCII, you could use this function.
	 *
	 * 	@param 	String
	 * 	@return String with non ASCII bytes removed
	 * 	@see 	utf8_strip_non_ascii_ctrl
	 */
	private static function utf8_strip_non_ascii( $str='' )
	{
	    ob_start();

	    while ( preg_match( '/^([\x00-\x7F]+)|([^\x00-\x7F]+)/S', $str, $matches ) )
	    {
	        if ( !isset( $matches[ 2 ] ) )
	        {
	        	echo $matches[ 0 ];
	        }
	        $str = substr( $str, strlen( $matches[ 0 ] ) );
	    }

	    $result = ob_get_contents();

	    ob_end_clean();

	    return $result;
	}

	/**
	 * 	Strip out device control codes in the ASCII range
	 * 	which are not permitted in XML. Note that this leaves
	 * 	multi-byte characters untouched - it only removes device control codes.
	 *
	 * 	@see 	http://hsivonen.iki.fi/producing-xml/#controlchar
	 * 	@param 	String
	 * 	@return String 	control codes removed
	 */
	private static function utf8_strip_ascii_ctrl( $str )
	{
	    ob_start();

	    while ( preg_match( '/^([^\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+)|([\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+)/S', $str, $matches ) )
	    {
	        if ( !isset( $matches[ 2 ]) )
	        {
	        	echo $matches[ 0 ];
	        }

	        $str = substr( $str, strlen( $matches[ 0 ] ) );
	    }

	    $result = ob_get_contents();

	    ob_end_clean();

	    return $result;
	}

	/**
	 * 	Replace accented UTF-8 characters by unaccented ASCII-7 "equivalents".
	 * 	The purpose of this function is to replace characters commonly found in Latin
	 * 	alphabets with something more or less equivalent from the ASCII range. This can
	 * 	be useful for converting a UTF-8 to something ready for a filename, for example.
	 * 	Following the use of this function, you would probably also pass the string
	 * 	through utf8_strip_non_ascii to clean out any other non-ASCII chars
	 * 	Use the optional parameter to just deaccent lower ($case = -1) or upper ($case = 1)
	 * 	letters. Default is to deaccent both cases ($case = 0)
	 *
	 * 	For a more complete implementation of transliteration, see the utf8_to_ascii package
	 * 	available from the phputf8 project downloads:
	 * 	http://prdownloads.sourceforge.net/phputf8
	 *
	 * 	@author Andreas Gohr <andi@splitbrain.org>
	 * 	@param 	String 	UTF-8 string
	 * 	@param 	int 	(optional) -1 lowercase only, +1 uppercase only, 1 both cases
	 * 	@param 	String 	UTF-8 with accented characters replaced by ASCII chars
	 * 	@return String 	accented chars replaced with ascii equivalents
	 */
	private static function utf8_accents_to_ascii( $str, $case=0 )
	{

	    static $UTF8_LOWER_ACCENTS = NULL;
	    static $UTF8_UPPER_ACCENTS = NULL;

	    if ( $case <= 0 )
	    {
	        if ( is_null($UTF8_LOWER_ACCENTS) )
	        {
	      		$UTF8_LOWER_ACCENTS = array(
				  'à' => 'a', 'ô' => 'o', 'ď' => 'd', 'ḟ' => 'f', 'ë' => 'e', 'š' => 's', 'ơ' => 'o',
				  'ß' => 'ss', 'ă' => 'a', 'ř' => 'r', 'ț' => 't', 'ň' => 'n', 'ā' => 'a', 'ķ' => 'k',
				  'ŝ' => 's', 'ỳ' => 'y', 'ņ' => 'n', 'ĺ' => 'l', 'ħ' => 'h', 'ṗ' => 'p', 'ó' => 'o',
				  'ú' => 'u', 'ě' => 'e', 'é' => 'e', 'ç' => 'c', 'ẁ' => 'w', 'ċ' => 'c', 'õ' => 'o',
				  'ṡ' => 's', 'ø' => 'o', 'ģ' => 'g', 'ŧ' => 't', 'ș' => 's', 'ė' => 'e', 'ĉ' => 'c',
				  'ś' => 's', 'î' => 'i', 'ű' => 'u', 'ć' => 'c', 'ę' => 'e', 'ŵ' => 'w', 'ṫ' => 't',
				  'ū' => 'u', 'č' => 'c', 'ö' => 'oe', 'è' => 'e', 'ŷ' => 'y', 'ą' => 'a', 'ł' => 'l',
				  'ų' => 'u', 'ů' => 'u', 'ş' => 's', 'ğ' => 'g', 'ļ' => 'l', 'ƒ' => 'f', 'ž' => 'z',
				  'ẃ' => 'w', 'ḃ' => 'b', 'å' => 'a', 'ì' => 'i', 'ï' => 'i', 'ḋ' => 'd', 'ť' => 't',
				  'ŗ' => 'r', 'ä' => 'ae', 'í' => 'i', 'ŕ' => 'r', 'ê' => 'e', 'ü' => 'ue', 'ò' => 'o',
				  'ē' => 'e', 'ñ' => 'n', 'ń' => 'n', 'ĥ' => 'h', 'ĝ' => 'g', 'đ' => 'd', 'ĵ' => 'j',
				  'ÿ' => 'y', 'ũ' => 'u', 'ŭ' => 'u', 'ư' => 'u', 'ţ' => 't', 'ý' => 'y', 'ő' => 'o',
				  'â' => 'a', 'ľ' => 'l', 'ẅ' => 'w', 'ż' => 'z', 'ī' => 'i', 'ã' => 'a', 'ġ' => 'g',
				  'ṁ' => 'm', 'ō' => 'o', 'ĩ' => 'i', 'ù' => 'u', 'į' => 'i', 'ź' => 'z', 'á' => 'a',
				  'û' => 'u', 'þ' => 'th', 'ð' => 'dh', 'æ' => 'ae', 'µ' => 'u', 'ĕ' => 'e',
	            );
	        }

	        $str = str_replace(
                array_keys( $UTF8_LOWER_ACCENTS ),
                array_values( $UTF8_LOWER_ACCENTS ),
                $str
            );
	    }

	    if ( $case >= 0 )
	    {
			if ( is_null( $UTF8_UPPER_ACCENTS ) )
			{
				$UTF8_UPPER_ACCENTS = array(
				  'À' => 'A', 'Ô' => 'O', 'Ď' => 'D', 'Ḟ' => 'F', 'Ë' => 'E', 'Š' => 'S', 'Ơ' => 'O',
				  'Ă' => 'A', 'Ř' => 'R', 'Ț' => 'T', 'Ň' => 'N', 'Ā' => 'A', 'Ķ' => 'K',
				  'Ŝ' => 'S', 'Ỳ' => 'Y', 'Ņ' => 'N', 'Ĺ' => 'L', 'Ħ' => 'H', 'Ṗ' => 'P', 'Ó' => 'O',
				  'Ú' => 'U', 'Ě' => 'E', 'É' => 'E', 'Ç' => 'C', 'Ẁ' => 'W', 'Ċ' => 'C', 'Õ' => 'O',
				  'Ṡ' => 'S', 'Ø' => 'O', 'Ģ' => 'G', 'Ŧ' => 'T', 'Ș' => 'S', 'Ė' => 'E', 'Ĉ' => 'C',
				  'Ś' => 'S', 'Î' => 'I', 'Ű' => 'U', 'Ć' => 'C', 'Ę' => 'E', 'Ŵ' => 'W', 'Ṫ' => 'T',
				  'Ū' => 'U', 'Č' => 'C', 'Ö' => 'Oe', 'È' => 'E', 'Ŷ' => 'Y', 'Ą' => 'A', 'Ł' => 'L',
				  'Ų' => 'U', 'Ů' => 'U', 'Ş' => 'S', 'Ğ' => 'G', 'Ļ' => 'L', 'Ƒ' => 'F', 'Ž' => 'Z',
				  'Ẃ' => 'W', 'Ḃ' => 'B', 'Å' => 'A', 'Ì' => 'I', 'Ï' => 'I', 'Ḋ' => 'D', 'Ť' => 'T',
				  'Ŗ' => 'R', 'Ä' => 'Ae', 'Í' => 'I', 'Ŕ' => 'R', 'Ê' => 'E', 'Ü' => 'Ue', 'Ò' => 'O',
				  'Ē' => 'E', 'Ñ' => 'N', 'Ń' => 'N', 'Ĥ' => 'H', 'Ĝ' => 'G', 'Đ' => 'D', 'Ĵ' => 'J',
				  'Ÿ' => 'Y', 'Ũ' => 'U', 'Ŭ' => 'U', 'Ư' => 'U', 'Ţ' => 'T', 'Ý' => 'Y', 'Ő' => 'O',
				  'Â' => 'A', 'Ľ' => 'L', 'Ẅ' => 'W', 'Ż' => 'Z', 'Ī' => 'I', 'Ã' => 'A', 'Ġ' => 'G',
				  'Ṁ' => 'M', 'Ō' => 'O', 'Ĩ' => 'I', 'Ù' => 'U', 'Į' => 'I', 'Ź' => 'Z', 'Á' => 'A',
				  'Û' => 'U', 'Þ' => 'Th', 'Ð' => 'Dh', 'Æ' => 'Ae', 'Ĕ' => 'E',
	       		);
	        }

	        $str = str_replace(
                array_keys( $UTF8_UPPER_ACCENTS ),
                array_values( $UTF8_UPPER_ACCENTS ),
                $str
            );
	    }

	    return $str;
	}

	private static function removeSpecialChars( $text='' )
	{
		return preg_replace( '/[^a-zA-Z0-9_%:[.:\\?&-]\\/-]/s', ' ', str_replace(array(':http','/http'),' http',$text ) );
	}

	protected static function simplifyText( $text )
	{
		return urldecode( stripslashes( $text ) );
	}

	protected static function unhtmlentities( $text )
	{
		$trans_tbl = '';

	   // replace litteral entities
	   if ( function_exists( 'get_html_translation_table' ) )
		   $trans_tbl = array_flip( get_html_translation_table( HTML_ENTITIES ) );

	   return strtr( $text, $trans_tbl );
	}

	public static function xml_entities( $text, $charset='UTF-8' )
	{
   		try
		{
			if ( defined( 'ENT_XML1' ) )
				return htmlspecialchars( $text, ENT_QUOTES | ENT_XML1, $charset, FALSE ); // ENT_XML1 const only available for PHP > 5.4
			else
			{
				return htmlspecialchars(
					strtr(
						$text,
				        array(
				            "<" => "&lt;",
				            ">" => "&gt;",
				            '"' => "&quot;",
				            "'" => "&apos;",
				            "&" => "&amp;"
				        )
				    ), ENT_QUOTES, $charset, FALSE
				);
			}
   		}
   		catch( Error $e )
   		{
	   		return strtr(
		        $text,
		        array(
		            "<" => "&lt;",
		            ">" => "&gt;",
		            '"' => "&quot;",
		            "'" => "&apos;",
		            "&" => "&amp;"
		        )
		    );
		}
	}

	private static function resolveUnicode( $text )
	{
		$special_chars = array(
			'&Agrave;' 	=> 'À',
			'&agrave;' 	=> 'à',
			'&Aacute;' 	=> 'Á',
			'&aacute;' 	=> 'á',
			'&Acirc;' 	=> 'Â',
			'&acirc;' 	=> 'â',
			'&Atilde;' 	=> 'Ã',
			'&atilde;' 	=> 'ã',
			'&Auml;' 	=> 'Ä',
			'&auml;' 	=> 'ä',
			'&Aring;'	=> 'Å',
			'&aring;' 	=> 'å',
			'&AElig;' 	=> 'Æ',
			'&aelig;' 	=> 'æ',
			'&Ccedil;' 	=> 'Ç',
			'&ccedil;' 	=> 'ç',
			'&ETH;' 	=> 'Ð',
			'&eth;' 	=> 'ð',
			'&Egrave;' 	=> 'È',
			'&egrave;' 	=> 'è',
			'&Eacute;' 	=> 'É',
			'&eacute;' 	=> 'é',
			'&Ecirc;' 	=> 'Ê',
			'&ecirc;' 	=> 'ê',
			'&Euml;' 	=> 'Ë',
			'&euml;' 	=> 'ë',
			'&Igrave;' 	=> 'Ì',
			'&igrave;' 	=> 'ì',
			'&Iacute;' 	=> 'Í',
			'&iacute;' 	=> 'í',
			'&Icirc;' 	=> 'Î',
			'&icirc;' 	=> 'î',
			'&Iuml;' 	=> 'Ï',
			'&iuml;' 	=> 'ï',
			'&Ntilde;' 	=> 'Ñ',
			'&ntilde;' 	=> 'ñ',
			'&Ograve;' 	=> 'Ò',
			'&ograve;' 	=> 'ò',
			'&Oacute;' 	=> 'Ó',
			'&oacute;' 	=> 'ó',
			'&Ocirc;' 	=> 'Ô',
			'&ocirc;' 	=> 'ô',
			'&Otilde;' 	=> 'Õ',
			'&otilde;' 	=> 'õ',
			'&Ouml;' 	=> 'Ö',
			'&ouml;' 	=> 'ö',
			'&Oslash;' 	=> 'Ø',
			'&oslash;' 	=> 'ø',
			'&OElig;' 	=> 'Œ',
			'&oelig;' 	=> 'œ',
			'&szlig;' 	=> 'ß',
			'&THORN;' 	=> 'Þ',
			'&thorn;' 	=> 'þ',
			'&Ugrave;'	=> 'Ù',
			'&ugrave;' 	=> 'ù',
			'&Uacute;' 	=> 'Ú',
			'&uacute;' 	=> 'ú',
			'&Ucirc;' 	=> 'Û',
			'&ucirc;' 	=> 'û',
			'&Uuml;' 	=> 'Ü',
			'&uuml;' 	=> 'ü',
			'&Yacute;' 	=> 'Ý',
			'&yacute;' 	=> 'ý',
			'&Yuml;' 	=> 'Ÿ',
			'&yuml;' 	=> 'ÿ',
			'&euro;'	=> '€',
			'&plusmn;'	=> '±',

			'&sbquo;' 	=> chr(130), // Single Low-9 Quotation Mark
        	'&fnof;' 	=> chr(131), // Latin Small Letter F With Hook
			'&bdquo;' 	=> chr(132), // Double Low-9 Quotation Mark
			'&hellip;' 	=> chr(133), // Horizontal Ellipsis
			'&dagger;' 	=> chr(134), // Dagger
			'&Dagger;' 	=> chr(135), // Double Dagger
			'&circ;' 	=> chr(136), // Modifier Letter Circumflex Accent
			'&permil;' 	=> chr(137), // Per Mille Sign
			'&Scaron;' 	=> chr(138), // Latin Capital Letter S With Caron
			'&lsaquo;' 	=> chr(139), // Single Left-Pointing Angle Quotation Mark
			'&OElig;' 	=> chr(140), // Latin Capital Ligature OE
			'&lsquo;' 	=> chr(145), // Left Single Quotation Mark
			'&rsquo;' 	=> chr(146), // Right Single Quotation Mark
			'&ldquo;' 	=> chr(147), // Left Double Quotation Mark
			'&rdquo;' 	=> chr(148), // Right Double Quotation Mark
			'&bull;' 	=> chr(149), // Bullet
			'&ndash;' 	=> chr(150), // En Dash
			'&mdash;' 	=> chr(151), // Em Dash
			'&tilde;' 	=> chr(152), // Small Tilde
			'&trade;' 	=> chr(153), // Trade Mark Sign
			'&scaron;' 	=> chr(154), // Latin Small Letter S With Caron
			'&rsaquo;' 	=> chr(155), // Single Right-Pointing Angle Quotation Mark
			'&oelig;' 	=> chr(156), // Latin Small Ligature OE
			'&Yuml;' 	=> chr(159)  // Latin Capital Letter Y With Diaeresis
       	);

		foreach( $special_chars as $el => &$char )
		{
			$text = str_replace( $el, $char, $text );
		}

		return $text;
	}

}