@props([
    'name' => 'address',
    'label' => 'Address',
    'required' => false,
    'placeholder' => 'Start typing address...',
    'value' => '',
    'includeCoordinates' => false,
    'country' => 'IN'
])

<div class="address-input-group" data-name="{{ $name }}">
    <label class="form-label">
        {{ $label }}
        @if($required) <span class="text-danger">*</span> @endif
    </label>

    <!-- Address Search Input -->
    <div class="input-group">
        <input type="text"
               class="form-control address-autocomplete"
               id="{{ $name }}_search"
               placeholder="{{ $placeholder }}"
               data-country="{{ $country }}"
               autocomplete="off">
        <button class="btn btn-outline-secondary" type="button" onclick="getCurrentLocation('{{ $name }}')">
            <i class="bi bi-geo-alt"></i>
        </button>
    </div>

    <!-- Address Components (Hidden inputs) -->
    <div class="row g-2 mt-2 address-components">
        <div class="col-md-12">
            <label class="form-label">Address Line 1 @if($required)<span class="text-danger">*</span>@endif</label>
            <input type="text"
                   class="form-control"
                   name="{{ $name }}_line_1"
                   id="{{ $name }}_line_1"
                   value="{{ old($name.'_line_1', $value['address_line_1'] ?? '') }}"
                   {{ $required ? 'required' : '' }}>
            <div class="invalid-feedback"></div>
        </div>

        <div class="col-md-12">
            <label class="form-label">Address Line 2</label>
            <input type="text"
                   class="form-control"
                   name="{{ $name }}_line_2"
                   id="{{ $name }}_line_2"
                   value="{{ old($name.'_line_2', $value['address_line_2'] ?? '') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">City @if($required)<span class="text-danger">*</span>@endif</label>
            <input type="text"
                   class="form-control"
                   name="{{ $name }}_city"
                   id="{{ $name }}_city"
                   value="{{ old($name.'_city', $value['city'] ?? '') }}"
                   {{ $required ? 'required' : '' }}>
            <div class="invalid-feedback"></div>
        </div>

        <div class="col-md-6">
            <label class="form-label">State @if($required)<span class="text-danger">*</span>@endif</label>
            <input type="text"
                   class="form-control"
                   name="{{ $name }}_state"
                   id="{{ $name }}_state"
                   value="{{ old($name.'_state', $value['state'] ?? '') }}"
                   {{ $required ? 'required' : '' }}>
            <div class="invalid-feedback"></div>
        </div>

        <div class="col-md-6">
            <label class="form-label">PIN Code @if($required)<span class="text-danger">*</span>@endif</label>
            <input type="text"
                   class="form-control"
                   name="{{ $name }}_pincode"
                   id="{{ $name }}_pincode"
                   pattern="[0-9]{6}"
                   value="{{ old($name.'_pincode', $value['pincode'] ?? '') }}"
                   {{ $required ? 'required' : '' }}>
            <div class="invalid-feedback"></div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Country</label>
            <select class="form-select"
                    name="{{ $name }}_country"
                    id="{{ $name }}_country">
                <option value="India" {{ old($name.'_country', $value['country'] ?? 'India') === 'India' ? 'selected' : '' }}>India</option>
                <option value="USA" {{ old($name.'_country', $value['country'] ?? '') === 'USA' ? 'selected' : '' }}>USA</option>
                <option value="UK" {{ old($name.'_country', $value['country'] ?? '') === 'UK' ? 'selected' : '' }}>UK</option>
            </select>
        </div>

        @if($includeCoordinates)
        <div class="col-md-6">
            <label class="form-label">Latitude</label>
            <input type="number"
                   class="form-control"
                   name="{{ $name }}_latitude"
                   id="{{ $name }}_latitude"
                   step="any"
                   value="{{ old($name.'_latitude', $value['latitude'] ?? '') }}"
                   readonly>
        </div>

        <div class="col-md-6">
            <label class="form-label">Longitude</label>
            <input type="number"
                   class="form-control"
                   name="{{ $name }}_longitude"
                   id="{{ $name }}_longitude"
                   step="any"
                   value="{{ old($name.'_longitude', $value['longitude'] ?? '') }}"
                   readonly>
        </div>
        @endif

        <!-- Hidden Google Place ID -->
        <input type="hidden"
               name="{{ $name }}_google_place_id"
               id="{{ $name }}_google_place_id"
               value="{{ old($name.'_google_place_id', $value['google_place_id'] ?? '') }}">
    </div>

    <!-- Validation Messages -->
    @error($name.'_line_1')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    @error($name.'_city')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    @error($name.'_state')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    @error($name.'_pincode')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

@once
@push('scripts')
<script>
let addressAutocompletes = {};

function initializeAddressInputs() {
    const addressInputs = document.querySelectorAll('.address-autocomplete');

    addressInputs.forEach(input => {
        const name = input.closest('.address-input-group').dataset.name;
        const country = input.dataset.country || 'IN';

        // Initialize Google Places Autocomplete
        if (typeof google !== 'undefined' && google.maps) {
            const autocomplete = new google.maps.places.Autocomplete(input, {
                componentRestrictions: { country: country },
                types: ['address']
            });

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                populateAddressFields(place, name);
            });

            addressAutocompletes[name] = autocomplete;
        }
    });
}

function populateAddressFields(place, name) {
    if (!place.address_components) return;

    const addressComponents = {
        street_number: '',
        route: '',
        locality: '',
        administrative_area_level_2: '',
        administrative_area_level_1: '',
        country: '',
        postal_code: ''
    };

    // Parse address components
    place.address_components.forEach(component => {
        const type = component.types[0];
        if (addressComponents.hasOwnProperty(type)) {
            addressComponents[type] = component.long_name;
        }
    });

    // Populate form fields
    const streetAddress = [addressComponents.street_number, addressComponents.route].filter(Boolean).join(' ');
    const city = addressComponents.locality || addressComponents.administrative_area_level_2;
    const state = addressComponents.administrative_area_level_1;
    const country = addressComponents.country;
    const pincode = addressComponents.postal_code;

    document.getElementById(`${name}_line_1`).value = streetAddress || place.name || '';
    document.getElementById(`${name}_city`).value = city || '';
    document.getElementById(`${name}_state`).value = state || '';
    document.getElementById(`${name}_pincode`).value = pincode || '';
    document.getElementById(`${name}_country`).value = country || 'India';
    document.getElementById(`${name}_google_place_id`).value = place.place_id || '';

    // Set coordinates if fields exist
    if (place.geometry && place.geometry.location) {
        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        const latField = document.getElementById(`${name}_latitude`);
        const lngField = document.getElementById(`${name}_longitude`);

        if (latField) latField.value = lat;
        if (lngField) lngField.value = lng;
    }

    // Clear any validation errors
    clearAddressValidationErrors(name);
}

function getCurrentLocation(name) {
    if (!navigator.geolocation) {
        showToast('Geolocation is not supported by this browser', 'warning');
        return;
    }

    const button = event.target.closest('button');
    const originalIcon = button.innerHTML;
    button.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i>';
    button.disabled = true;

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Reverse geocoding
            const geocoder = new google.maps.Geocoder();
            const latlng = new google.maps.LatLng(lat, lng);

            geocoder.geocode({ location: latlng }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const place = results[0];
                    populateAddressFields(place, name);

                    // Update the search input
                    const searchInput = document.getElementById(`${name}_search`);
                    searchInput.value = place.formatted_address;
                } else {
                    showToast('Unable to retrieve address for this location', 'warning');
                }

                button.innerHTML = originalIcon;
                button.disabled = false;
            });
        },
        function(error) {
            let message = 'Unable to retrieve your location';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message = 'Location access denied by user';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = 'Location information is unavailable';
                    break;
                case error.TIMEOUT:
                    message = 'Location request timed out';
                    break;
            }

            showToast(message, 'warning');
            button.innerHTML = originalIcon;
            button.disabled = false;
        },
        {
            timeout: 10000,
            enableHighAccuracy: true,
            maximumAge: 300000 // 5 minutes
        }
    );
}

function clearAddressValidationErrors(name) {
    const fields = ['line_1', 'city', 'state', 'pincode'];
    fields.forEach(field => {
        const input = document.getElementById(`${name}_${field}`);
        if (input) {
            input.classList.remove('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = '';
            }
        }
    });
}

function validateAddress(name, required = false) {
    const fields = {
        [`${name}_line_1`]: 'Address Line 1',
        [`${name}_city`]: 'City',
        [`${name}_state`]: 'State',
        [`${name}_pincode`]: 'PIN Code'
    };

    let isValid = true;

    Object.keys(fields).forEach(fieldName => {
        const input = document.getElementById(fieldName);
        if (input && required && !input.value.trim()) {
            input.classList.add('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = `${fields[fieldName]} is required`;
            }
            isValid = false;
        }
    });

    // Validate PIN code format
    const pincodeInput = document.getElementById(`${name}_pincode`);
    if (pincodeInput && pincodeInput.value && !/^\d{6}$/.test(pincodeInput.value)) {
        pincodeInput.classList.add('is-invalid');
        const feedback = pincodeInput.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = 'PIN Code must be 6 digits';
        }
        isValid = false;
    }

    return isValid;
}

// Initialize when Google Maps loads
function initGooglePlaces() {
    if (typeof google !== 'undefined' && google.maps) {
        initializeAddressInputs();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if Google Maps is already loaded
    if (typeof google !== 'undefined' && google.maps) {
        initializeAddressInputs();
    } else {
        // Wait for Google Maps to load
        window.addEventListener('load', function() {
            setTimeout(initializeAddressInputs, 1000);
        });
    }
});

// CSS for loading spinner
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endonce