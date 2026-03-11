@extends('site.app.layout')

@section('content')

<section class="bg0 p-t-75 p-b-85">
<div class="container">

<h3 class="mtext-111 cl2 m-b-30">Edit Address</h3>

@if ($errors->any())
<div class="alert alert-danger">
<ul class="mb-0">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif


<form action="{{ route('shipping.addresses.update',$address) }}" method="POST">
@csrf
@method('PUT')


<div class="row">

<div class="col-md-12 m-b-20">

<label class="m-b-10">Search Address</label>

<input
type="text"
id="autocomplete"
class="form-control"
placeholder="Search address or place"
value="{{ trim(($address->street ?? '') . ' ' . ($address->district ?? '') . ' ' . ($address->city ?? '') . ' ' . ($address->country ?? '')) }}"
>

</div>


<div class="col-md-6 m-b-20">
<input type="text"
name="label"
class="form-control"
placeholder="Label"
value="{{ old('label',$address->label) }}">
</div>


<div class="col-md-6 m-b-20">
<input type="text"
name="full_name"
class="form-control"
placeholder="Full Name"
value="{{ old('full_name',$address->full_name) }}"
required>
</div>


<div class="col-md-6 m-b-20">
<input type="text"
name="phone"
class="form-control"
placeholder="Phone"
value="{{ old('phone',$address->phone) }}"
required>
</div>


<div class="col-md-6 m-b-20">
<input type="text"
name="country"
id="country"
class="form-control"
placeholder="Country"
value="{{ old('country',$address->country) }}"
required>
</div>


<div class="col-md-6 m-b-20">
<input type="text"
name="city"
id="city"
class="form-control"
placeholder="City"
value="{{ old('city',$address->city) }}"
required>
</div>


<div class="col-md-6 m-b-20">
<input type="text"
name="district"
id="district"
class="form-control"
placeholder="District"
value="{{ old('district',$address->district) }}">
</div>


<div class="col-md-6 m-b-20">
<input type="text"
name="street"
id="street"
class="form-control"
placeholder="Street"
value="{{ old('street',$address->street) }}">
</div>


<div class="col-md-3 m-b-20">
<input type="text"
name="building_no"
id="building_no"
class="form-control"
placeholder="Building No"
value="{{ old('building_no',$address->building_no) }}">
</div>


<div class="col-md-3 m-b-20">
<input type="text"
name="apartment_no"
class="form-control"
placeholder="Apartment No"
value="{{ old('apartment_no',$address->apartment_no) }}">
</div>


<div class="col-md-6 m-b-20">
<input type="text"
name="postal_code"
id="postal_code"
class="form-control"
placeholder="Postal Code"
value="{{ old('postal_code',$address->postal_code) }}">
</div>



<div class="col-md-6 m-b-20">
<input type="text"
id="lat_display"
class="form-control"
placeholder="Latitude"
value="{{ old('lat',$address->lat) }}"
readonly>
</div>


<div class="col-md-6 m-b-20">
<input type="text"
id="lng_display"
class="form-control"
placeholder="Longitude"
value="{{ old('lng',$address->lng) }}"
readonly>
</div>


<input type="hidden" name="lat" id="lat" value="{{ old('lat',$address->lat) }}">
<input type="hidden" name="lng" id="lng" value="{{ old('lng',$address->lng) }}">



<div class="col-md-12 m-b-20">
<textarea
name="notes"
class="form-control"
rows="4"
placeholder="Notes">{{ old('notes',$address->notes) }}</textarea>
</div>



<div class="col-md-12 m-b-20">

<label>
<input
type="checkbox"
name="is_default"
value="1"
{{ old('is_default',$address->is_default) ? 'checked':'' }}
>

Set as default address
</label>

</div>



<div class="col-md-12">
<button type="submit" class="btn btn-dark">
Update Address
</button>
</div>


</div>

</form>

</div>
</section>



<script>

let autocomplete;

function initGoogleAddressAutocomplete() {

const input = document.getElementById('autocomplete');

const latInput = document.getElementById('lat');
const lngInput = document.getElementById('lng');

const latDisplay = document.getElementById('lat_display');
const lngDisplay = document.getElementById('lng_display');


latDisplay.value = latInput.value;
lngDisplay.value = lngInput.value;



autocomplete = new google.maps.places.Autocomplete(input,{
types:['address'],
componentRestrictions:{country:['sa']},
fields:['address_components','geometry']
});



autocomplete.addListener('place_changed',function(){

const place = autocomplete.getPlace();

if(!place.geometry){
return;
}



let streetNumber='';
let route='';
let city='';
let district='';
let country='';
let postalCode='';



place.address_components.forEach(component=>{

const types=component.types;



if(types.includes('street_number')){
streetNumber=component.long_name;
}



if(types.includes('route')){
route=component.long_name;
}



if(types.includes('locality')){
city=component.long_name;
}



if(
types.includes('sublocality') ||
types.includes('sublocality_level_1') ||
types.includes('neighborhood')
){
district=component.long_name;
}



if(types.includes('country')){
country=component.long_name;
}



if(types.includes('postal_code')){
postalCode=component.long_name;
}

});



document.getElementById('street').value=route;
document.getElementById('building_no').value=streetNumber;
document.getElementById('city').value=city;
document.getElementById('district').value=district;
document.getElementById('country').value=country;
document.getElementById('postal_code').value=postalCode;



const lat=place.geometry.location.lat();
const lng=place.geometry.location.lng();



latInput.value=lat;
lngInput.value=lng;

latDisplay.value=lat;
lngDisplay.value=lng;


});

}

window.initGoogleAddressAutocomplete=initGoogleAddressAutocomplete;

</script>



<script async
src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initGoogleAddressAutocomplete">
</script>

@endsection
