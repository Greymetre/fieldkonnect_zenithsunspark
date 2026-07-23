<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Joining Details (Greymeter)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #def4f9;
        }

        .content {
            border: 1px solid lightgrey;
            border-radius: 7px;
            padding: 30px 20px 5px 20px;
            position: relative;
        }

        .content-frm {
            border: 1px solid lightgrey;
            border-radius: 7px;
            padding: 50px 20px;
            position: relative;
        }

        .content::before {
            content: ' ';
            position: absolute;
            width: 100%;
            height: 10px;
            top: 0px;
            background: #24b3d4;
            left: 0;
            border-radius: 7px 7px 0px 0px;
        }

        hr {
            width: 658px;
            margin-left: -20px;
        }

        input.form-check-input {
            border: 2px solid;
            width: 20px;
            height: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="text-center mt-3">
            <img src="{{asset('assets/img/new_joining_logo.png')}}" alt="">
        </div>
        <div class="row mt-4">
            <div class="col-md-2"></div>
            <div class="col-md-8 content bg-light">
                <h3>Silver Consumer Electricals Private Limited</h3>
                <p class="mt-5">We at Silver believe our Employees are the greatest assets and we always strive to know every little bit about them. Knowing them properly leads us to serve them well. Our Culture says We respect & trust each other to accomplish our mutual Goals. "Challange Accepted" is our Mantra. We request you to take few moments to write your Personal and Professional details please.</p>
                <hr>
                <p class="text-danger">* Indicates required question</p>
            </div>
            <div class="col-md-2"></div>
        </div>
        <form action="{{route('joining-form.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="email" name="email" id="email" placeholder="Your email" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="first_name" id="first_name" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="middle_name">Middle Name <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="middle_name" id="middle_name" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="last_name" id="last_name" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="gender">Gender <span class="text-danger">*</span></label>
                        <div class="form-check mt-4">
                            <input class="form-check-input mr-3" type="radio" name="gender" value="Male" id="Male" required>
                            <label class="form-check-label" for="gender"> Male </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input mr-3" type="radio" name="gender" value="Female" id="Female" required>
                            <label class="form-check-label" for="gender"> Female </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="dob">Date of Birth <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="date" name="dob" id="dob" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="mobile_number">Mobile Number <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="number" name="mobile_number" id="mobile_number" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="contact_number">Emergency Contact Number <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="number" name="contact_number" id="contact_number" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="father_name">Father Name <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="father_name" id="father_name" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="father_occupation">Father Occupation <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="father_occupation" id="father_occupation" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="mother_name">Mother Name <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="mother_name" id="mother_name" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="mother_occupation">Mother Occupation <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="mother_occupation" id="mother_occupation" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="marital_status">Marital Status <span class="text-danger">*</span></label>
                        <div class="form-check mt-4">
                            <input class="form-check-input mr-3" type="radio" name="marital_status" value="Married" id="Married" required>
                            <label class="form-check-label" for="marital_status"> Married </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input mr-3" type="radio" name="marital_status" value="Unmarried" id="Unmarried" required>
                            <label class="form-check-label" for="marital_status"> Unmarried </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="spouse_name">Spouse's Name</label>
                        <input class="form-control mt-4" type="text" name="spouse_name" id="spouse_name" placeholder="Your answer">
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="spouse_dob">Spouse's DOB</label>
                        <input class="form-control mt-4" type="date" name="spouse_dob" id="spouse_dob" placeholder="Your answer">
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="spouse_education">Spouse's Education</label>
                        <input class="form-control mt-4" type="text" name="spouse_education" id="spouse_education" placeholder="Your answer">
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="spouse_occupation">Spouse's Occupation</label>
                        <input class="form-control mt-4" type="text" name="spouse_occupation" id="spouse_occupation" placeholder="Your answer">
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="anniversary">Date of Anniversary</label>
                        <input class="form-control mt-4" type="date" name="anniversary" id="anniversary" placeholder="Your answer">
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="present_address">Present Address <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="present_address" id="present_address" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="present_city">City <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="present_city" id="present_city" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="present_state">State <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="present_state" id="present_state" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="present_pincode">Pincode <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="present_pincode" id="present_pincode" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light" style="padding-top:10px">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="same_addr" id="same_addr">
                        <label class="form-check-label" for="same_addr"> Same as Present </label>
                    </div>
                    <div class="form-group mt-4">
                        <label for="permanent_address">Permanent Address <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="permanent_address" id="permanent_address" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="permanent_city">City <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="permanent_city" id="permanent_city" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="permanent_state">State <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="permanent_state" id="permanent_state" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="permanent_pincode">Pincode <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="permanent_pincode" id="permanent_pincode" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="pan">PAN Number <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="pan" id="pan" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="aadhar">Adhar Number <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="aadhar" id="aadhar" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="driving_licence">Driving Licence Number <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="driving_licence" id="driving_licence" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="blood_group">Blood Group <span class="text-danger">*</span></label>
                        <select class="form-select" name="blood_group" id="blood_group">
                            <option value="" disabled selected>Choose</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="language">Language Know</label>
                        <table class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Read</th>
                                    <th>Speak</th>
                                    <th>Write</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td scope="col">English</td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="r" name="english[]">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="s" name="english[]">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="w" name="english[]">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="col">Hindi</td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="r" name="hindi[]">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="s" name="hindi[]">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="w" name="hindi[]">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="col">Gujarati</td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="r" name="gujarati[]">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="s" name="gujarati[]">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="w" name="gujarati[]">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="col">Other</td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="r" name="other[]">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="s" name="other[]">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="w" name="other[]">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="other_language">Other Language</label>
                        <input class="form-control mt-4" type="text" name="other_language" id="other_language" placeholder="Your answer">
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="qualification">Education Qualification <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="qualification" id="qualification" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="experience">Experience Details <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="experience" id="experience" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="skill">Skill <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="text" name="skill" id="skill" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="occupy">Gadgets/Electronic Devices/Vehicles/Own House that you occupy <span class="text-danger">*</span></label>
                        <div class="form-check mt-5">
                            <input class="form-check-input" type="checkbox" value="Laptop" name="occupy[]">
                            <label class="form-check-label" for="flexCheckDefault">
                                Laptop
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="Smart Phone" name="occupy[]">
                            <label class="form-check-label" for="flexCheckDefault">
                                Smart Phone
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="Two Wheelers" name="occupy[]">
                            <label class="form-check-label" for="flexCheckDefault">
                                Two Wheelers
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="Four Wheelers" name="occupy[]">
                            <label class="form-check-label" for="flexCheckDefault">
                                Four Wheelers
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="Own House" name="occupy[]">
                            <label class="form-check-label" for="flexCheckDefault">
                                Own House
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="branch">Branch <span class="text-danger">*</span></label>
                        <select class="form-select" name="branch" id="blood_group">
                            <option value="" disabled selected>Your answer</option>
                            @if($branchs && count($branchs) > 0)
                            @foreach($branchs as $branch)
                            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="department">Department <span class="text-danger">*</span></label>
                        <select class="form-select" name="department" id="blood_group">
                            <option value="" disabled selected>Your answer</option>
                            @if($departments && count($departments) > 0)
                            @foreach($departments as $department)
                            <option value="{{$department->id}}">{{$department->name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="date_of_joining">Date of Joining <span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="date" name="date_of_joining" id="date_of_joining" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="designation">Designation <span class="text-danger">*</span></label>
                        <select class="form-select" name="designation" id="blood_group">
                            <option value="" disabled selected>Your answer</option>
                            @if($designations && count($designations) > 0)
                            @foreach($designations as $designation)
                            <option value="{{$designation->id}}">{{$designation->designation_name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="adhar_images">Adhar Card (Front & Back Side Both)<span class="text-danger">*</span></label>
                        <input multiple class="form-control mt-4" type="file" accept="image/png, image/gif, image/jpeg" name="adhar_images[]" id="adhar_images" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="pan_images">PAN Card<span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="file" accept="image/png, image/gif, image/jpeg" name="pan_images" id="pan_images" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="passport_images">Passport size Photo<span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="file" accept="image/png, image/gif, image/jpeg" name="passport_images" id="passport_images" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="ssc_images">SSC Result<span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="file" accept="image/png, image/gif, image/jpeg" name="ssc_images" id="ssc_images" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="hsc_images">HSC Result<span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="file" accept="image/png, image/gif, image/jpeg" name="hsc_images" id="hsc_images" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="graduation_images">Graduation Certificate<span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="file" accept="image/png, image/gif, image/jpeg" name="graduation_images" id="graduation_images" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="birth_images">School Leaving Certificate/ Birth Certificate<span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="file" accept="image/png, image/gif, image/jpeg" name="birth_images" id="birth_images" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="relieving_images">Experience Certificate cum Relieving Letter from Previous Employer<span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="file" accept="image/png, image/gif, image/jpeg" name="relieving_images" id="relieving_images" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="last_salray_images">Last salary Slip<span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="file" accept="image/png, image/gif, image/jpeg" name="last_salray_images" id="last_salray_images" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="bank_images">Bank Detail<span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="file" accept="image/png, image/gif, image/jpeg" name="bank_images" id="bank_images" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 content-frm bg-light">
                    <div class="form-group">
                        <label for="offer_images">Salary Structure with signature (Offered by Silver)<span class="text-danger">*</span></label>
                        <input class="form-control mt-4" type="file" accept="image/png, image/gif, image/jpeg" name="offer_images" id="offer_images" placeholder="Your answer" required>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>



            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <p>A copy of your responses will be emailed to the address you provided.</p>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <input type="submit" class="btn btn-info">
                </div>
                <div class="col-md-2"></div>
            </div>

        </form>
        <div class="row mt-3"></div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#same_addr').change(function() {
                if (this.checked) {

                    $('[name^="permanent"]').prop('readonly', true);

                    $('#permanent_address').val($('#present_address').val());
                    $('#permanent_city').val($('#present_city').val());
                    $('#permanent_state').val($('#present_state').val());
                    $('#permanent_pincode').val($('#present_pincode').val());
                } else {

                    $('[name^="permanent"]').prop('readonly', false);

                    $('#permanent_address').val('');
                    $('#permanent_city').val('');
                    $('#permanent_state').val('');
                    $('#permanent_pincode').val('');
                }
            });

            $('#present_address, #present_city, #present_state, #present_pincode').on('input', function() {
                if ($('#same_addr').prop('checked')) {
                    $('#permanent_address').val($('#present_address').val());
                    $('#permanent_city').val($('#present_city').val());
                    $('#permanent_state').val($('#present_state').val());
                    $('#permanent_pincode').val($('#present_pincode').val());
                }
            });
        });
    </script>
</body>

</html>