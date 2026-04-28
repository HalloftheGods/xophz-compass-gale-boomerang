(function() {
	'use strict';

	const COOKIE_NAME = '_xc_gale_wind';
	const COOKIE_DAYS = 365;

	/**
	 * Set a cookie
	 */
	function setCookie(name, value, days) {
		let expires = '';
		if (days) {
			const date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			expires = '; expires=' + date.toUTCString();
		}
		document.cookie = name + '=' + (value || '') + expires + '; path=/; SameSite=Lax';
	}

	/**
	 * Get a cookie
	 */
	function getCookie(name) {
		const nameEQ = name + '=';
		const ca = document.cookie.split(';');
		for (let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) === ' ') c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
		}
		return null;
	}

	/**
	 * Parse URL parameters
	 */
	function getParams() {
		const searchParams = new URLSearchParams(window.location.search);
		const params = {};
		for (const [key, value] of searchParams.entries()) {
			params[key] = value;
		}
		return params;
	}

	/**
	 * Track the "Original Wind" (UTM or Referer)
	 */
	function trackWind() {
		// Only track if we don't already have the cookie (first visit)
		if (!getCookie(COOKIE_NAME)) {
			const params = getParams();
			const windData = {
				source: params.utm_source || '',
				medium: params.utm_medium || '',
				campaign: params.utm_campaign || '',
				term: params.utm_term || '',
				content: params.utm_content || '',
				referer: document.referrer || '',
				first_visit: new Date().toISOString()
			};

			// Only save if there's actual attribution data
			if (windData.source || windData.referer) {
				setCookie(COOKIE_NAME, JSON.stringify(windData), COOKIE_DAYS);
			}
		}
	}

	// Initialize
	window.addEventListener('load', trackWind);

})();
