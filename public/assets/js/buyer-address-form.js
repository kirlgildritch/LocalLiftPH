(function () {
    const API_BASE = 'https://psgc.cloud/api/v2';
    const cache = new Map();

    function normalizeCollection(payload) {
        if (Array.isArray(payload)) {
            return payload;
        }

        if (Array.isArray(payload?.data)) {
            return payload.data;
        }

        return [];
    }

    async function fetchCollection(url) {
        if (!cache.has(url)) {
            cache.set(url, fetch(url).then(async (response) => {
                if (!response.ok) {
                    throw new Error(`Location request failed: ${response.status}`);
                }

                return normalizeCollection(await response.json());
            }));
        }

        return cache.get(url);
    }

    function getField(form, name) {
        return form.querySelector(`[name="${name}"]`);
    }

    function getSelectedCode(select) {
        return select?.selectedOptions?.[0]?.dataset?.code || '';
    }

    function showLocationError(form, message) {
        const errorNode = form.querySelector('[data-location-feedback]');

        if (!errorNode) {
            return;
        }

        errorNode.hidden = !message;
        errorNode.textContent = message || '';
    }

    function setLoading(select, placeholder) {
        if (!select) {
            return;
        }

        select.innerHTML = '';

        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;
        option.disabled = true;
        option.selected = true;
        select.appendChild(option);
        select.disabled = true;
    }

    function resetSelect(select, placeholder) {
        if (!select) {
            return;
        }

        select.innerHTML = '';

        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;
        option.disabled = true;
        option.selected = true;
        select.appendChild(option);
        select.disabled = false;
    }

    function populateSelect(select, items, placeholder, selectedValue, options) {
        resetSelect(select, placeholder);

        const values = Array.isArray(items) ? items : [];
        const config = options || {};

        if (!values.length && config.syntheticOption) {
            values.push(config.syntheticOption);
        }

        values.forEach((item) => {
            const option = document.createElement('option');
            option.value = item.name;
            option.textContent = item.name;
            option.dataset.code = item.code || '';

            if (selectedValue && item.name === selectedValue) {
                option.selected = true;
            }

            select.appendChild(option);
        });

        if (selectedValue && !Array.from(select.options).some((option) => option.value === selectedValue)) {
            const fallback = document.createElement('option');
            fallback.value = selectedValue;
            fallback.textContent = selectedValue;
            fallback.selected = true;
            select.appendChild(fallback);
        }

        select.disabled = false;
    }

    async function loadRegions(form, selectedValue) {
        const regionSelect = getField(form, 'region');

        setLoading(regionSelect, 'Loading regions...');

        const regions = await fetchCollection(`${API_BASE}/regions`);
        populateSelect(regionSelect, regions, 'Select region', selectedValue);
    }

    async function loadProvinces(form, selectedValue) {
        const regionSelect = getField(form, 'region');
        const provinceSelect = getField(form, 'province');
        const citySelect = getField(form, 'city');
        const barangaySelect = getField(form, 'barangay');
        const regionCode = getSelectedCode(regionSelect);

        resetSelect(citySelect, 'Select city / municipality');
        resetSelect(barangaySelect, 'Select barangay');

        if (!regionCode) {
            resetSelect(provinceSelect, 'Select province');
            return;
        }

        setLoading(provinceSelect, 'Loading provinces...');

        const provinces = await fetchCollection(`${API_BASE}/regions/${encodeURIComponent(regionCode)}/provinces`);
        const selectedRegionName = regionSelect.value;

        populateSelect(
            provinceSelect,
            provinces,
            provinces.length ? 'Select province' : 'Select province / area',
            selectedValue,
            !provinces.length ? {
                syntheticOption: {
                    code: '',
                    name: selectedValue || selectedRegionName,
                },
            } : undefined
        );
    }

    async function loadCities(form, selectedValue) {
        const regionSelect = getField(form, 'region');
        const provinceSelect = getField(form, 'province');
        const citySelect = getField(form, 'city');
        const barangaySelect = getField(form, 'barangay');
        const regionCode = getSelectedCode(regionSelect);
        const provinceCode = getSelectedCode(provinceSelect);

        resetSelect(barangaySelect, 'Select barangay');

        if (!regionCode || !provinceSelect.value) {
            resetSelect(citySelect, 'Select city / municipality');
            return;
        }

        setLoading(citySelect, 'Loading cities / municipalities...');

        const url = provinceCode
            ? `${API_BASE}/provinces/${encodeURIComponent(provinceCode)}/cities-municipalities`
            : `${API_BASE}/regions/${encodeURIComponent(regionCode)}/cities-municipalities`;
        const cities = await fetchCollection(url);

        populateSelect(citySelect, cities, 'Select city / municipality', selectedValue);
    }

    async function loadBarangays(form, selectedValue) {
        const citySelect = getField(form, 'city');
        const barangaySelect = getField(form, 'barangay');
        const cityCode = getSelectedCode(citySelect);

        if (!cityCode) {
            resetSelect(barangaySelect, 'Select barangay');
            return;
        }

        setLoading(barangaySelect, 'Loading barangays...');

        const barangays = await fetchCollection(`${API_BASE}/cities-municipalities/${encodeURIComponent(cityCode)}/barangays`);
        populateSelect(barangaySelect, barangays, 'Select barangay', selectedValue);
    }

    async function hydrateLocationFields(form, selectedValues) {
        showLocationError(form, '');

        try {
            await loadRegions(form, selectedValues.region);
            await loadProvinces(form, selectedValues.province);
            await loadCities(form, selectedValues.city);
            await loadBarangays(form, selectedValues.barangay);
        } catch (error) {
            console.error(error);
            showLocationError(form, 'Location options could not be loaded right now. Please refresh and try again.');
        }
    }

    function collectSelectedValues(form, overrides) {
        return {
            region: overrides?.region ?? getField(form, 'region')?.dataset.selected ?? getField(form, 'region')?.value ?? '',
            province: overrides?.province ?? getField(form, 'province')?.dataset.selected ?? getField(form, 'province')?.value ?? '',
            city: overrides?.city ?? getField(form, 'city')?.dataset.selected ?? getField(form, 'city')?.value ?? '',
            barangay: overrides?.barangay ?? getField(form, 'barangay')?.dataset.selected ?? getField(form, 'barangay')?.value ?? '',
        };
    }

    function attachChangeHandlers(form) {
        const regionSelect = getField(form, 'region');
        const provinceSelect = getField(form, 'province');
        const citySelect = getField(form, 'city');

        if (!regionSelect || regionSelect.dataset.initialized === '1') {
            return;
        }

        regionSelect.dataset.initialized = '1';

        regionSelect.addEventListener('change', async () => {
            showLocationError(form, '');
            try {
                await loadProvinces(form, '');
                await loadCities(form, '');
                resetSelect(getField(form, 'barangay'), 'Select barangay');
            } catch (error) {
                console.error(error);
                showLocationError(form, 'Location options could not be loaded right now. Please refresh and try again.');
            }
        });

        provinceSelect?.addEventListener('change', async () => {
            showLocationError(form, '');
            try {
                await loadCities(form, '');
                resetSelect(getField(form, 'barangay'), 'Select barangay');
            } catch (error) {
                console.error(error);
                showLocationError(form, 'Location options could not be loaded right now. Please refresh and try again.');
            }
        });

        citySelect?.addEventListener('change', async () => {
            showLocationError(form, '');
            try {
                await loadBarangays(form, '');
            } catch (error) {
                console.error(error);
                showLocationError(form, 'Location options could not be loaded right now. Please refresh and try again.');
            }
        });
    }

    async function initForm(form, overrides) {
        attachChangeHandlers(form);

        const selectedValues = collectSelectedValues(form, overrides);
        await hydrateLocationFields(form, selectedValues);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-ph-address-form').forEach((form) => {
            initForm(form);
        });
    });

    window.LocalLiftAddressForm = {
        init: initForm,
    };
})();
