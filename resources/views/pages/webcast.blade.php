<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Royal Canin | Webcast</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
    
    <!-- Vendor CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/simplebar@latest/dist/simplebar.css">
    
    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/main.min.css') }}" rel="stylesheet">
</head>

<body>
    <!-- TOP BANNER -->
    <div class="topbar">
        <div class="container py-2 text-center">
            <img src="{{ asset('assets/img/royalcanin2025.png') }}" class="img-fluid" alt="Royal Canin">
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="#">Hello, {{ Auth::user()->full_name ?? Auth::user()->name }}</a>
                
                <ul class="navbar-nav m-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('webcast') }}">Live Session</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('previous-sessions') }}">Previous Session</a>
                    </li>
                </ul>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-logout">
                        <i class="bi bi-person-circle"></i> Logout
                    </button>
                </form>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main pb-3">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Video Wrapper -->
                <div class="col-lg-8 col-md-8 col-12">
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ route('player') }}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Poll Sidebar Button -->
        <div class="sidebar-btn sidebar-btn-left" id="pollSidebarCollapse">
            <img src="{{ asset('assets/img/poll.png') }}" class="img-fluid" alt="Poll">
            <span class="blob"></span>
            <div>ANSWER THE POLL</div>
        </div>

        <!-- Question Sidebar Button -->
        <div class="sidebar-btn sidebar-btn-right" id="questionSidebarCollapse">
            <span class="blob"></span>
            <img src="{{ asset('assets/img/ask-questio.png') }}" class="img-fluid" alt="Question">
            <div>ASK A QUESTION</div>
        </div>
    </main>

    <!-- Reaction Buttons -->
    <div class="emoticon-wrapper">
        <button id="loveBtn"><i class="bi bi-heart-fill"></i></button>
        <button id="likeBtn"><i class="bi bi-hand-thumbs-up-fill"></i></button>
        <button id="applause"><img src="{{ asset('assets/img/icons/clap.png') }}" alt="Applause"></button>
    </div>
    
    <div class="reaction-container" id="heartContainer"></div>

    <!-- Poll Sidebar -->
    <nav id="pollSidebar" class="sidebar">
        <div class="sidebar-header">
            <h3>POLL</h3>
            <div id="dismissPollSidebar" class="dismiss">
                <i class="bi bi-arrow-left"></i>
            </div>
        </div>
        <div class="sidebar-body">
            <div class="poll-wrapper">
                <div id="poll" class="mb-4"></div>
            </div>
        </div>
    </nav>

    <!-- Question Sidebar -->
    <nav id="questionSidebar" class="sidebar-right">
        <div class="sidebar-header">
            <div id="dismissQuestionSidebar" class="dismiss dismiss-right">
                <i class="bi bi-arrow-right"></i>
            </div>
            <h3>ASK QUESTION</h3>
        </div>
        <div class="sidebar-body">
            <h4 class="text-dark">Please ask your questions, it'll be answered during Q&A session.</h4>
            <div class="question-wrapper">
                <div class="question-box mt-4 mt-md-0">
                    <div id="message"></div>
                    <form id="question-form" method="POST" action="{{ route('submit-question') }}">
                        @csrf
                        <textarea class="form-control input-rounded" name="question_input" rows="4" placeholder="Enter your question and click on submit!"></textarea>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-canin">Submit Question</button>
                        </div>
                    </form>
                </div>
                
                <div class="view-question-box">
                    <div class="chat_window">
                        <ul class="messages" id="messages"></ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/simplebar@latest/dist/simplebar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>
    <script src="https://cdn.pubnub.com/sdk/javascript/pubnub.7.3.1.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <script>
        var userId = '{{ Auth::id() }}';
        
        // Initialize PubNub
        var pubnub = new PubNub({
            subscribeKey: 'sub-c-e0a8a9b4-a5b2-4ac1-865c-9c198705ad92',
            userId: userId,
        });
        
        pubnub.subscribe({
            channels: ["poll_status"]
        });
        
        // Initialize Pusher
        var pusher = new Pusher('aca7938088f631ee68de', {
            cluster: 'ap2'
        });
        
        var channel = pusher.subscribe('my-channel');
        
        $(document).ready(function() {
            // Sidebar functionality
            $('#dismissPollSidebar, #dismissQuestionSidebar').on('click', function() {
                $('#pollSidebar, #questionSidebar').removeClass('active');
            });
            
            $('#pollSidebarCollapse').on('click', function() {
                $('#pollSidebar').addClass('active');
            });
            
            $('#questionSidebarCollapse').on('click', function() {
                $('#questionSidebar').addClass('active');
            });
            
            // Poll and Question functions
            checkPoll();
            setInterval(showQuestion, 30000);
        });
        
        // Poll listeners
        pubnub.addListener({
            message: (messageEvent) => {
                if (messageEvent.channel === 'poll_status') {
                    checkPoll();
                }
            },
        });
        
        channel.bind('my-event', function(data) {
            checkPoll();
        });
        
        function checkPoll() {
            $.ajax({
                url: '{{ route("check-poll") }}',
                type: 'POST',
                data: { request: 1, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response === 'NO_POLL_ACTIVE' || response === '') {
                        $("#pollSidebar").removeClass("active");
                        $('#poll').html('<div class="alert alert-info">No active poll available at the moment.</div>');
                    } else {
                        $('#pollSidebarCollapse').click();
                        $('#poll').html(response);
                    }
                }
            });
        }
        
        $(document).on("click", "#but_vote", function(e) {
            e.preventDefault();
            var checkedPoll = $("#poll input[name='poll']:checked").val();
            
            if (checkedPoll !== undefined) {
                $.ajax({
                    url: '{{ route("submit-vote") }}',
                    type: 'POST',
                    data: { request: 2, poll: checkedPoll, _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response == 1) checkPoll();
                    }
                });
            } else {
                alert('Please select an option.');
            }
        });
        
        function showQuestion() {
            $.ajax({
                url: '{{ route("get-questions") }}',
                type: 'POST',
                data: { request: 3, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    $('#messages').html(response);
                }
            });
        }
        
        // Question form validation
        $('#question-form').validate({
            rules: { question_input: { required: true } },
            messages: { question_input: 'Please enter your question!' },
            submitHandler: function(form) {
                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: $(form).serialize(),
                    success: function(data) {
                        var message = '';
                        if (data == 1) {
                            message = '<div class="alert alert-success">Question Submitted Successfully!</div>';
                            $(form)[0].reset();
                        } else if (data == 2) {
                            message = '<div class="alert alert-warning">Already Submitted</div>';
                        } else {
                            message = '<div class="alert alert-danger">Something went wrong!</div>';
                        }
                        $('#message').html(message).show();
                        setTimeout(() => $('#message').fadeOut(), 3000);
                    }
                });
            }
        });
        
        // Reactions
        $('#loveBtn, #likeBtn, #applause').on('click', function() {
            var reactionType = this.id === 'loveBtn' ? 'love' : (this.id === 'likeBtn' ? 'like' : 'applause');
            createReactionAnimation(reactionType);
            storeReaction(reactionType);
            
            if (reactionType === 'applause') {
                new Audio('{{ asset("assets/audio/audience-clapping.mp3") }}').play();
            }
        });
        
        function createReactionAnimation(type) {
            const heartContainer = document.getElementById('heartContainer');
            for (let i = 0; i < 30; i++) {
                const element = document.createElement('div');
                element.classList.add(type === 'love' ? 'heart' : (type === 'like' ? 'like' : 'clap'));
                element.style.left = `${Math.random() * 100}vw`;
                element.style.bottom = `-${Math.random() * 10}vh`;
                const size = Math.random() * 30 + 10;
                element.style.width = `${size}px`;
                element.style.height = `${size}px`;
                element.style.animationDuration = `${Math.random() * 4 + 4}s`;
                element.style.opacity = Math.random();
                heartContainer.appendChild(element);
                element.addEventListener('animationend', () => element.remove());
            }
        }
        
        function storeReaction(reactionType) {
            $.ajax({
                url: '{{ route("store-reaction") }}',
                type: 'POST',
                data: { reaction: reactionType, _token: '{{ csrf_token() }}' },
                error: (xhr) => console.error("Error:", xhr.responseText)
            });
        }
    </script>
</body>

</html>