<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Thank You - GI Horizons</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom Fonts (Assuming you have D-DIN-PRO) -->
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/font.css') }}" />

    <!-- External Main CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/login.min.css') }}" />
    
    <style>
        .thankyou-container {
            max-width: 1280px;
            margin: 0 auto;
            min-height: 80vh;
            display: flex;
            align-items: center;
        }
        
        .text-red {
            color: #dc2626;
        }
        
        .text-gray {
            color: #4a4a4a;
        }
        
        .thankyou-title {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.2;
        }
        
        @media (min-width: 768px) {
            .thankyou-title {
                font-size: 4rem;
            }
        }
        
        .content-text {
            font-size: 1rem;
            color: #4a4a4a;
            line-height: 1.7;
        }
        
        .content-text p {
            margin-bottom: 1rem;
        }
        
        .whats-next-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .whats-next-box {
            background-color: #f9fafb;
            border-left: 4px solid #dc2626;
            padding: 1.5rem;
            border-radius: 8px;
        }
        
        .whats-next-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        
        .whats-next-list li {
            padding-left: 1.5rem;
            position: relative;
            margin-bottom: 0.75rem;
            line-height: 1.5;
            color: #4a4a4a;
        }
        
        .whats-next-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #dc2626;
            font-weight: bold;
        }
        
        .whats-next-list li:last-child {
            margin-bottom: 0;
        }
        
        .quote-box {
            background-color: #ffffff;
            border: 1px solid #fca5a5;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .quote-title {
            font-size: 1.25rem;
            font-weight: 600;
            line-height: 1.4;
            margin-bottom: 1rem;
        }
        
        .quote-text {
            font-size: 0.95rem;
            color: #4a4a4a;
            line-height: 1.6;
            margin-bottom: 0;
        }
        
        @media (max-width: 991px) {
            .thankyou-container {
                min-height: auto;
                padding: 2rem 0;
            }
            
            .quote-box {
                margin-top: 2rem;
            }
        }
    </style>
</head>
<body>
    
    <div class="container-fluid p-4 p-md-5">
        <div class="thankyou-container">
            
            <div class="row align-items-center g-5">
                
                <!-- LEFT COLUMN: Text Content -->
                <div class="col-lg-6 col-12">
                    
                    <!-- Main Heading -->
                    <h1 class="thankyou-title">
                        <span class="text-red">THANK YOU</span><br>
                        <span class="text-gray">FOR REGISTERING!</span>
                    </h1>

                    <!-- Body Text -->
                    <div class="content-text mt-4">
                        <p>
                            You are now successfully registered for GI Horizons: Advancing Veterinary
                            Gastroenterology, brought to you by Royal Canin.
                        </p>
                        <p>
                            We look forward to welcoming you to six insightful sessions led by Dr. K. G. Umesh, covering critical
                            aspects of gastrointestinal health, diagnostics, and nutrition in small animals.
                        </p>
                    </div>

                    <!-- What's Next Section -->
                    <div class="whats-next-section mt-5">
                        <h2 class="whats-next-title text-red">What's Next?</h2>
                        
                        <div class="whats-next-box">
                            <ul class="whats-next-list">
                                <li>Check your email for the confirmation and webinar access link</li>
                                <li>Save the dates: 27 May – 21 Oct 2026, every Wednesday, 1 PM – 2 PM IST</li>
                                <li>Prepare to engage in evidence-based learning and case discussions</li>
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Quote Box -->
                <div class="col-lg-4 offset-lg-2 col-12 align-self-center">
                    <!-- Quote Box -->
                    <div class="quote-box">
                        <h3 class="quote-title text-red">
                            Royal Canin thanks you for your commitment to advancing veterinary science.
                        </h3>
                        <p class="quote-text mt-3">
                            Together, we can improve patient outcomes and strengthen the veterinary community.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>