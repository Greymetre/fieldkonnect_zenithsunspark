<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheme Product PDF</title>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"> -->
    <style>
    </style>
</head>

<body>
    <!-- <img style="width: 100%; height: 100%;" src="file://{{ public_path('assets/img/gift_catelog_img.jpg') }}" alt="Gift Catalog Image"> -->
    <table style="width:100%; border:1px solid #eee; border-spacing: 0; margin-right:20px;">
        <thead style="background-color:#eee;">
            <tr>
                <th style="font-size:12px; padding: 15px 5px; width: 10%;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">No</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 10%; border:1px solid #eee;">Category</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 10%; border:1px solid #eee;">Subcategory</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 10%; border:1px solid #eee;">Product</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 20%; border:1px solid #eee;">Active Points</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 10%; border:1px solid #eee;">Provision Points</th>
                <th style="font-size:12px; padding: 15px 5px;  width: 30%; border:1px solid #eee;">Total Points </th>

            </tr>
        </thead>
        <tbody>
            @if(isset($data) && count($data)>0)
            @foreach($data as $key => $val)
            @if($groupw == 'yes')
            <tr>
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee; text-align : center">{{$key+1 ?? ''}}</td>
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($val['categories']) ? $val['categories'] : ''}}</td>

                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($val['subcategories']) ? $val['subcategories'] : ''}}</td>

                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;width: 100%;max-width: 200px;min-width: 200px;white-space: break-spaces;text-wrap: inherit;">{{isset($val['products']) ? $val['products'] : ''}}</td>
      
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($val['active_point']) ? $val['active_point'] : ''}}</td>

                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($val['provision_point']) ? $val['provision_point'] : ''}}</td>

                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($val['points']) ? $val['points'] : ''}}</td>
            </tr>
            @else@if($groupw == 'no')
            <tr>
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee; text-align : center">{{$key+1 ?? ''}}</td>
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($val->categories->category_name) ? $val->categories->category_name : ''}}</td>

                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($val->subcategories->subcategory_name) ? $val->subcategories->subcategory_name : ''}}</td>

                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($val->products->product_name) ? $val->products->product_name : ''}}</td>
      
                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($val->active_point) ? $val->active_point : ''}}</td>

                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($val->provision_point) ? $val->provision_point : ''}}</td>

                <td style="font-size:12px; padding: 8px 10px;  font-family: 'Roboto', sans-serif;; border:1px solid #eee;">{{isset($val->points) ? $val->points : ''}}</td>
            </tr> 
            @endif
            @endforeach
            @else

            @endif
        </tbody>
    </table>
</body>

</html>