<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Catalogue PDF</title>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"> -->
    <style>
    </style>
</head>

<body>
    <img style="width: 100%; height: 100%;" src="file://{{ public_path('assets/img/gift_catelog_img.jpg') }}" alt="Gift Catalog Image">
    <table style="width:100%; border:1px solid #eee; border-spacing: 0; margin-right:20px;">
        <thead style="background-color:#eee;">
            <tr>
                <th style="font-size:12px; padding: 15px 5px; width: 10%;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">No</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 10%; border:1px solid #eee;">Subcategory Name</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 10%; border:1px solid #eee;">Brand Name</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 20%; border:1px solid #eee;">Product Image</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 10%; border:1px solid #eee;">Model Name</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 30%; border:1px solid #eee;">Product Name</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 10%; border:1px solid #eee;">Points</th>

            </tr>
        </thead>
        <tbody>
            @if(isset($gifts) && count($gifts)>0)
            @foreach($gifts as $key => $gift)
            <tr>
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee; text-align : center">{{$key+1 ?? ''}}</td>
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($gift->subcategories->subcategory_name) ? $gift->subcategories->subcategory_name : ''}}</td>

                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($gift->brands->brand_name) ? $gift->brands->brand_name : ''}}</td>
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee; text-align:center;">
                    <img src="https://silver.fieldkonnect.io/public/uploads/{{ $gift->product_image }}" width="100px" alt="Product Image" />
                </td>
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($gift->models->model_name) ? $gift->models->model_name : ''}}</td>
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($gift->product_name) ? $gift->product_name : ''}}</td>
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($gift->points) ? $gift->points : ''}}</td>
            </tr>
            @endforeach
            @else

            @endif
        </tbody>
    </table>
</body>

</html>