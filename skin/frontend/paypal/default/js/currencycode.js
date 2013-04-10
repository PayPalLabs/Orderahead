//  This file is part of the jQuery formatCurrency Plugin.
//
//    The jQuery formatCurrency Plugin is free software: you can redistribute it
//    and/or modify it under the terms of the GNU General Public License as published 
//    by the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.

//    The jQuery formatCurrency Plugin is distributed in the hope that it will
//    be useful, but WITHOUT ANY WARRANTY; without even the implied warranty 
//    of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License along with 
//    the jQuery formatCurrency Plugin.  If not, see <http://www.gnu.org/licenses/>.

(function($) {
        $.formatCurrency = {};

	$.formatCurrency.regions = []; 
        
	$.formatCurrency.regions['ZAR'] = {
		symbol: 'R',
		positiveFormat: '%s %n',
		negativeFormat: '%s-%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['ETB'] = {
		symbol: 'ETB',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['AED'] = {
		symbol: 'د.إ.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['BHD'] = {
		symbol: 'د.ب.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['DZD'] = {
		symbol: 'د.ج.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['EGP'] = {
		symbol: 'ج.م.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['IQD'] = {
		symbol: 'د.ع.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['JOD'] = {
		symbol: 'د.ا.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['KWD'] = {
		symbol: 'د.ك.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['LBP'] = {
		symbol: 'ل.ل.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['LYD'] = {
		symbol: 'د.ل.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['MAD'] = {
		symbol: 'د.م.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['OMR'] = {
		symbol: 'ر.ع.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['QAR'] = {
		symbol: 'ر.ق.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['SAR'] = {
		symbol: 'ر.س.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['SYP'] = {
		symbol: 'ل.س.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['TND'] = {
		symbol: 'د.ت.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['YER'] = {
		symbol: 'ر.ي.‏',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['CLP'] = {
		symbol: '$',
		positiveFormat: '%s %n',
		negativeFormat: '-%s %n',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['INR'] = {
		symbol: 'ট',
		positiveFormat: '%n%s',
		negativeFormat: '%s -%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['AZN'] = {
		symbol: 'ман.',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['RUB'] = {
		symbol: 'һ.',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['BYR'] = {
		symbol: 'р.',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['BGN'] = {
		symbol: 'лв',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['BDT'] = {
		symbol: '৳',
		positiveFormat: '%s %n',
		negativeFormat: '%s -%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['CNY'] = {
		symbol: '¥',
		positiveFormat: '%s%n',
		negativeFormat: '%s-%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['EUR'] = {
		symbol: '€',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['BAM'] = {
		symbol: 'КМ',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['CZK'] = {
		symbol: 'Kč',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['GBP'] = {
		symbol: '£',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['DKK'] = {
		symbol: 'kr',
		positiveFormat: '%s %n',
		negativeFormat: '%s -%n',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['CHF'] = {
		symbol: 'SFr.',
		positiveFormat: '%s %n',
		negativeFormat: '%s-%n',
		decimalSymbol: '.',
		digitGroupSymbol: '\'',
		groupDigits: true
	};

	$.formatCurrency.regions['de'] = {
		symbol: '€',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['MVR'] = {
		symbol: 'ރ.',
		positiveFormat: '%n %s',
		negativeFormat: '%n %s-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['en-029'] = {
		symbol: '$',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['AUD'] = {
		symbol: '$',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['BZD'] = {
		symbol: 'BZ$',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['CAD'] = {
		symbol: '$',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['JMD'] = {
		symbol: 'J$',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['MYR'] = {
		symbol: 'RM',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['NZD'] = {
		symbol: '$',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['PHP'] = {
		symbol: 'Php',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['SGD'] = {
		symbol: '$',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['TTD'] = {
		symbol: 'TT$',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['USD'] = {
		symbol: '$',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['ZWL'] = {
		symbol: 'Z$',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['ARS'] = {
		symbol: '$',
		positiveFormat: '%s %n',
		negativeFormat: '%s-%n',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['BOB'] = {
		symbol: '$b',
		positiveFormat: '%s %n',
		negativeFormat: '(%s %n)',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['COP'] = {
		symbol: '$',
		positiveFormat: '%s %n',
		negativeFormat: '(%s %n)',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['CRC'] = {
		symbol: '₡',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['DOP'] = {
		symbol: 'RD$',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['GTQ'] = {
		symbol: 'Q',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['HNL'] = {
		symbol: 'L.',
		positiveFormat: '%s %n',
		negativeFormat: '%s -%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['MXN'] = {
		symbol: '$',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['NIO'] = {
		symbol: 'C$',
		positiveFormat: '%s %n',
		negativeFormat: '(%s %n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['PAB'] = {
		symbol: 'B/.',
		positiveFormat: '%s %n',
		negativeFormat: '(%s %n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['PEN'] = {
		symbol: 'S/.',
		positiveFormat: '%s %n',
		negativeFormat: '%s -%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['PYG'] = {
		symbol: 'Gs',
		positiveFormat: '%s %n',
		negativeFormat: '(%s %n)',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['SVC'] = {
		symbol: '$',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['UYU'] = {
		symbol: '$U',
		positiveFormat: '%s %n',
		negativeFormat: '(%s %n)',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['VEF'] = {
		symbol: 'Bs',
		positiveFormat: '%s %n',
		negativeFormat: '%s -%n',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['es'] = {
		symbol: '€',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['IRR'] = {
		symbol: 'ريال',
		positiveFormat: '%s %n',
		negativeFormat: '%s%n-',
		decimalSymbol: '/',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['fr'] = {
		symbol: '€',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['NGN'] = {
		symbol: 'N',
		positiveFormat: '%s %n',
		negativeFormat: '%s-%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['ILS'] = {
		symbol: '₪',
		positiveFormat: '%s %n',
		negativeFormat: '%s-%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['HRK'] = {
		symbol: 'kn',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['HUF'] = {
		symbol: 'Ft',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['AMD'] = {
		symbol: 'դր.',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['IDR'] = {
		symbol: 'Rp',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['ISK'] = {
		symbol: 'kr.',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['it'] = {
		symbol: '€',
		positiveFormat: '%s %n',
		negativeFormat: '-%s %n',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['JPY'] = {
		symbol: '¥',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['ja'] = {
		symbol: '¥',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['GEL'] = {
		symbol: 'Lari',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['KZT'] = {
		symbol: 'Т',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '-',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['KHR'] = {
		symbol: '៛',
		positiveFormat: '%n%s',
		negativeFormat: '-%n%s',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['KRW'] = {
		symbol: '₩',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['KGS'] = {
		symbol: 'сом',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: '-',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['LAK'] = {
		symbol: '₭',
		positiveFormat: '%n%s',
		negativeFormat: '(%n%s)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['LTL'] = {
		symbol: 'Lt',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['LVL'] = {
		symbol: 'Ls',
		positiveFormat: '%s %n',
		negativeFormat: '-%s %n',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['MKD'] = {
		symbol: 'ден.',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['MNT'] = {
		symbol: '₮',
		positiveFormat: '%n%s',
		negativeFormat: '-%n%s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['BND'] = {
		symbol: '$',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['NOK'] = {
		symbol: 'kr',
		positiveFormat: '%s %n',
		negativeFormat: '%s -%n',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['NPR'] = {
		symbol: 'रु',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['PLN'] = {
		symbol: 'zł',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['AFN'] = {
		symbol: '؋',
		positiveFormat: '%s%n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['BRL'] = {
		symbol: 'R$',
		positiveFormat: '%s %n',
		negativeFormat: '-%s %n',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['RON'] = {
		symbol: 'lei',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['RWF'] = {
		symbol: 'RWF',
		positiveFormat: '%s %n',
		negativeFormat: '%s-%n',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['SEK'] = {
		symbol: 'kr',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['LKR'] = {
		symbol: 'රු.',
		positiveFormat: '%s %n',
		negativeFormat: '(%s %n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['ALL'] = {
		symbol: 'Lek',
		positiveFormat: '%n%s',
		negativeFormat: '-%n%s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['CSD'] = {
		symbol: 'Дин.',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['KES'] = {
		symbol: 'S',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['TJS'] = {
		symbol: 'т.р.',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ';',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['THB'] = {
		symbol: '฿',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['TMT'] = {
		symbol: 'm.',
		positiveFormat: '%n%s',
		negativeFormat: '-%n%s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['TRY'] = {
		symbol: 'TL',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['UAH'] = {
		symbol: 'грн.',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['PKR'] = {
		symbol: 'Rs',
		positiveFormat: '%s%n',
		negativeFormat: '%s%n-',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['UZS'] = {
		symbol: 'сўм',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['VND'] = {
		symbol: '₫',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: '.',
		groupDigits: true
	};

	$.formatCurrency.regions['XOF'] = {
		symbol: 'XOF',
		positiveFormat: '%n %s',
		negativeFormat: '-%n %s',
		decimalSymbol: ',',
		digitGroupSymbol: ' ',
		groupDigits: true
	};

	$.formatCurrency.regions['HKD'] = {
		symbol: 'HK$',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['MOP'] = {
		symbol: 'MOP',
		positiveFormat: '%s%n',
		negativeFormat: '(%s%n)',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['TWD'] = {
		symbol: 'NT$',
		positiveFormat: '%s%n',
		negativeFormat: '-%s%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};

	$.formatCurrency.regions['zh'] = {
		symbol: '¥',
		positiveFormat: '%s%n',
		negativeFormat: '%s-%n',
		decimalSymbol: '.',
		digitGroupSymbol: ',',
		groupDigits: true
	};
})(jQuery);
