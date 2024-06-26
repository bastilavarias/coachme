export function useMisc() {
    return {
        debounce(fn, delay) {
            let timeout;

            return (...args) => {
                if (timeout) {
                    clearTimeout(timeout);
                }

                timeout = setTimeout(() => {
                    fn(...args);
                }, delay);
            };
        },

        toImageURL(image) {
            let src = '';
            if (image) {
                if (image.path) {
                    src = `${import.meta.env.VITE_BASE_API_URL}/storage/${
                        image.path
                    }`;
                } else {
                    src = image.url;
                }
            }
            return src;
        },
    };
}
