import ky from 'ky';

interface HttpFunctionPayload {
    route: string;
    body?: any;
    url?: any;
    transform?: any;
}
interface HttpResponse {
    success: boolean;
    message;
    code?: number;
    slug?: string;
    data?: Array;
    pagination: Array;
}

const baseURL = import.meta.env.VITE_BASE_API_URL;

const api = ky.extend({
    timeout: false,
    hooks: {
        beforeRequest: [
            async (request) => {
                const savedAccessToken =
                    window.localStorage.getItem('access_token');
                if (savedAccessToken) {
                    request.headers.append(
                        'Authorization',
                        `Bearer ${savedAccessToken}`
                    );
                }
            },
        ],
    },
});

const toFormData = (obj: any) => {
    const formData = new FormData();
    function appendFormData(data: object, rootKey: string) {
        if (Array.isArray(data)) {
            data.forEach((value, index) => {
                appendFormData(value, `${rootKey}[${index}]`);
            });
        } else if (
            typeof data === 'object' &&
            data !== null &&
            !(data instanceof File)
        ) {
            for (let key in data) {
                if (data.hasOwnProperty(key)) {
                    if (rootKey === '') {
                        appendFormData(data[key], key);
                    } else {
                        appendFormData(data[key], `${rootKey}.${key}`);
                    }
                }
            }
        } else {
            formData.append(rootKey, data);
        }
    }
    appendFormData(obj, '');
    return formData;
};
const toURLEndpoint = (route: string, newURL: string) =>
    route && !newURL ? `${baseURL}/api/${route}` : newURL;
const toURLSearchParams = (payload: HttpFunctionPayload) => {
    let queries = [];
    Object.keys(payload).forEach((key) => {
        const value = payload[key];
        if (Array.isArray(value)) {
            if (value) {
                value.forEach((value, index) =>
                    queries.push(
                        `${key}[${index}]=${encodeURIComponent(
                            value.toString()
                        ).replace(/%20/g, '+')}`
                    )
                );
            }
        } else {
            if (value) {
                queries.push(
                    `${key}=${encodeURIComponent(value.toString()).replace(
                        /%20/g,
                        '+'
                    )}`
                );
            }
        }
    });
    return queries.join('&');
};
const toReadableResponse = async (
    type: string,
    body: any
): Promise<HttpResponse> => {
    if (type === 'complete') {
        return {
            success: true,
            message: body.message,
            data: body.data,
        };
    }
    const error = await body.response.json();
    if (error) {
        return error;
    }
    return {
        success: false,
        message: 'Server error.',
    };
};

const apiClient = {
    get: async ({ route, url }: HttpFunctionPayload) => {
        return await api.get(toURLEndpoint(route, url)).json();
    },
    post: async ({ route, body, url, transform }: HttpFunctionPayload) => {
        if (transform && transform === 'form-data') {
            return await api
                .post(toURLEndpoint(route, url), {
                    body: toFormData(body),
                })
                .json();
        }
        return await api
            .post(toURLEndpoint(route, url), {
                json: body,
            })
            .json();
    },
    delete: async ({ route, body, url }: HttpFunctionPayload) => {
        return await api
            .delete(toURLEndpoint(route, url), {
                data: body,
            })
            .json();
    },
    toURLSearchParams: (payload: any) => {
        let queries = [];
        Object.keys(payload).forEach((key) => {
            const value = payload[key];
            if (Array.isArray(value)) {
                if (value) {
                    value.forEach((value, index) =>
                        queries.push(
                            `${key}[${index}]=${encodeURIComponent(
                                value.toString()
                            ).replace(/%20/g, '+')}`
                        )
                    );
                }
            } else {
                if (value) {
                    queries.push(
                        `${key}=${encodeURIComponent(value.toString()).replace(
                            /%20/g,
                            '+'
                        )}`
                    );
                }
            }
        });
        return queries.join('&');
    },
    toURLSearchParams,
    toReadableResponse,
};
export default apiClient;
