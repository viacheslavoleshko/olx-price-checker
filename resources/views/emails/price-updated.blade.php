<!DOCTYPE html>
<html>

<head>
    <title>Price Updated</title>
</head>

<body>
    <h1>Price Updated</h1>
    <p>The price for the advert "{{ $advert->title }}" has been updated.</p>
    <p>New Price: {{ $price->value }} {{ $price->currency }}</p>
    <p><a href="{{ $advert->url }}">View Advert</a></p>
</body>

</html>