const fexios = {
  baseUrl: typeof ajaxurl !== 'undefined' ? ajaxurl : '',

  async request(endpoint, options = {}) {
    // Absolute URL అయితే baseUrl కలపకూడదు
    const url = /^https?:\/\//i.test(endpoint)
      ? endpoint
      : `${this.baseUrl}${endpoint}`;

	// creating new config without modifying original options object
    const config = {
      ...options,
      method: options.method || 'GET'
    };

	// Preparing body if data available
    if (options.data instanceof FormData) {
		// Do not set Content-Type with FormData; browser sets it automatically.
		config.body = options.data;		
    } else if (options.data !== undefined && options.data !== null) {
		config.headers = {
			'Content-Type': 'application/json',
			...options.headers
		};
		config.body = JSON.stringify(options.data);
    }

    // Axios-style behavior: HTTP errors will automatically be thrown
    const response = await fetch(url, config);

	// Reading Response JSON safely
    const contentType = response.headers.get('content-type') || '';

    let data;

    if (contentType.includes('application/json')) {
      data = await response.json().catch(() => ({}));
    } else {
      data = await response.text();
    }

    // HTTP error
    if (!response.ok) {
      throw {
        message: `HTTP Error: ${response.status}`,
        status: response.status,
        data
      };
    }

    // Axios-style response
    return {
      data,
      status: response.status
    };
  },

  // GET shortcut
  get(endpoint, options = {}) {
    return this.request(endpoint, {
      ...options,
      method: 'GET'
    });
  },

  // POST shortcut
  post(endpoint, data, options = {}) {
    return this.request(endpoint, {
      ...options,
      method: 'POST',
      data
    });
  }
};