<!DOCTYPE html>
<html>

<head>
    <title>Price Updated</title>
</head>

<body>
    <h1>Price Updated</h1>
    <p>The price for the advert "{{ $advert->title }}" has been updated.</p>
    <p>New Price: 
        @if ($price->value === null)
            @if ($price->budget)
                for free
            @elseif ($price->trade)
                trade
            @endif
        @else
            {{ $price->value }} {{ $price->currency }} {{ $price->negotiable ? '(negotiable)' : '' }}
        @endif
    </p>
    <p><a href="{{ $advert->url }}">View Advert</a></p>
</body>

</html>