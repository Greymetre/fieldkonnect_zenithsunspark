<x-app-layout>

    <!-- resources/views/components/403.blade.php -->
    <div style="
    height: 100vh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
">
        <div style="
        
    background: linear-gradient(135deg, #2E3192 0%, #1BFFFF 100%);
        padding: 60px;
        border-radius: 20px;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        text-align: center;
        color: white;
        max-width: 500px;
    ">
            <div style="font-size: 100px; font-weight: bold; text-shadow: 2px 2px 10px rgba(0,0,0,0.3);">403</div>
            <h2 style="font-size: 28px; margin: 20px;">Access Forbidden</h2>
            <p style="font-size: 16px; line-height: 1.6;">
                Oops! You don’t have permission to view this page.<br>
                Please contact your administrator if you believe this is an error.
            </p>
            <a href="{{ url('/customers') }}" style="
            display: inline-block;
            margin-top: 25px;
            padding: 12px 25px;
            background-color: #ffffff22;
            color: white;
            border: 1px solid white;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        " onmouseover="this.style.backgroundColor='#ffffff44'" onmouseout="this.style.backgroundColor='#ffffff22'">
                Go to Homepage
            </a>
        </div>
    </div>


</x-app-layout>