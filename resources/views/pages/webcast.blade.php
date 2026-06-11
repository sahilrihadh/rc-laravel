@extends('layouts.master')

@section('title', 'Webcast | Royal Canin')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/simplebar@latest/dist/simplebar.css">
<link href="{{ asset('assets/css/main.min.css') }}" rel="stylesheet">
<style>
    /* Additional styles for better UX */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #dc2626;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 8px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .btn-loading {
        opacity: 0.7;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <!-- Video Wrapper -->
        <div class="col-lg-8 col-md-8 col-12">
            <div class="ratio ratio-16x9">
                <iframe src="{{ asset('player.php') }}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe>
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

<!-- Reactions -->
@include('partials.reactions')

<!-- Poll Sidebar -->
@include('partials.poll-sidebar')

<!-- Question Sidebar -->
@include('partials.question-sidebar')
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://unpkg.com/simplebar@latest/dist/simplebar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<script>
    // ==================== CONFIGURATION ====================
    const userId = '{{ Auth::id() }}';
    const csrfToken = '{{ csrf_token() }}';

    // Set Axios defaults
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // ==================== PUSHER SETUP ====================
    const pusher = new Pusher('aca7938088f631ee68de', {
        cluster: 'ap2',
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        }
    });

    const pollChannel = pusher.subscribe('poll-channel');
    const questionChannel = pusher.subscribe('question-channel');

    // ==================== REACTION RATE LIMITING ====================
    const reactionCooldown = {
        love: false,
        like: false,
        applause: false,
        lastReactionTime: 0,
        minInterval: 3000, // 3 seconds minimum between ANY reactions
        
        // Check if specific reaction type can be submitted
        canSubmit: function(type) {
            const now = Date.now();
            // Check global cooldown
            if (now - this.lastReactionTime < this.minInterval) {
                console.log(`Please wait ${Math.ceil((this.minInterval - (now - this.lastReactionTime))/1000)} seconds before reacting again`);
                return false;
            }
            // Check specific reaction cooldown
            if (this[type]) {
                console.log(`You already submitted a ${type} reaction recently`);
                return false;
            }
            return true;
        },
        
        // Set cooldown for reaction type
        setCooldown: function(type) {
            const now = Date.now();
            this.lastReactionTime = now;
            this[type] = true;
            
            // Reset specific reaction cooldown after 10 seconds
            setTimeout(() => {
                this[type] = false;
            }, 10000);
        }
    };

    // Store submitted reactions in session to prevent duplicate database entries
    let submittedReactions = JSON.parse(sessionStorage.getItem('submittedReactions') || '{}');

    // ==================== INITIALIZATION ====================
    $(document).ready(function() {
        initializeSidebars();
        initializePoll();
        initializeQuestions();
        initializeReactions();
        initializeActivityTracking();
    });

    // ==================== SIDEBAR FUNCTIONS ====================
    function initializeSidebars() {
        $('#dismissPollSidebar, #dismissQuestionSidebar').on('click', function() {
            $('#pollSidebar, #questionSidebar').removeClass('active');
        });

        $('#pollSidebarCollapse').on('click', function() {
            $('#pollSidebar').addClass('active');
        });

        $('#questionSidebarCollapse').on('click', function() {
            $('#questionSidebar').addClass('active');
        });
    }

    // ==================== POLL FUNCTIONS ====================
    function initializePoll() {
        pollChannel.bind('poll-status-changed', function(data) {
            console.log('Poll status changed:', data);
            checkPoll();
        });

        // Initial poll check
        checkPoll();
    }

    async function checkPoll() {
        try {
            const response = await axios.post('{{ route("check.poll") }}', {
                request: 1
            });

            // Handle response - Laravel returns JSON with poll.html
            if (response.data && response.data.has_poll === true && response.data.poll && response.data.poll.html) {
                // Active poll with HTML
                $('#poll').html(response.data.poll.html);
                $('#pollSidebar').addClass('active');
            } 
            else if (response.data === 'NO_POLL_ACTIVE' || response.data === '') {
                // No active poll
                closePoll();
            }
            else if (typeof response.data === 'string' && response.data.length > 0) {
                // Fallback: treat as HTML string
                $('#poll').html(response.data);
                $('#pollSidebar').addClass('active');
            }
            else {
                closePoll();
            }
        } catch (error) {
            console.error('Error checking poll:', error);
            closePoll();
        }
    }

    function closePoll() {
        $("#pollSidebar").removeClass("active");
        $('#poll').html('<div class="alert alert-info">No active poll available at the moment.</div>');
    }

    // Submit vote handler
    $(document).on("click", "#but_vote", async function(e) {
        e.preventDefault();

        const checkedPoll = $("#poll input[name='poll']:checked").val();

        if (!checkedPoll) {
            alert('Please select an option.');
            return;
        }

        const $btn = $(this);
        const originalText = $btn.text();

        try {
            $btn.prop('disabled', true).html('<span class="loading-spinner"></span> Submitting...');

            const response = await axios.post('{{ route("submit.poll.vote") }}', {
                request: 2,
                poll: checkedPoll
            });

            if (response.data.success === true || response.data == 1) {
                await checkPoll(); // Refresh to show results with progress bars
            } else {
                alert(response.data.message || 'Failed to submit vote.');
                $btn.prop('disabled', false).html(originalText);
            }
        } catch (error) {
            console.error('Vote submission error:', error);
            alert('An error occurred. Please try again.');
            $btn.prop('disabled', false).html(originalText);
        }
    });

    // ==================== QUESTION FUNCTIONS ====================
    function initializeQuestions() {
        questionChannel.bind('new-question', function(data) {
            console.log('New question received:', data);
            showQuestion();
        });

        showQuestion();
        setInterval(showQuestion, 30000);
        
        // Initialize form validation
        initializeQuestionForm();
    }

    function initializeQuestionForm() {
        // Make sure the form exists before validating
        if ($('#question-form').length) {
            // Remove any existing validation to prevent duplicates
            if ($('#question-form').data('validator')) {
                $('#question-form').data('validator').destroy();
            }
            
            $('#question-form').validate({
                rules: {
                    question_input: {
                        required: true,
                        minlength: 5
                    }
                },
                messages: {
                    question_input: {
                        required: 'Please enter your question!',
                        minlength: 'Please enter at least 5 characters'
                    }
                },
                submitHandler: async function(form, event) {
                    event.preventDefault(); // Prevent default submission
                    
                    const $form = $(form);
                    const $submitBtn = $form.find('button[type="submit"]');
                    const originalText = $submitBtn.text();
                    
                    console.log('Form submitting...'); // Debug log
                    console.log('Form action:', $form.attr('action')); // Debug log
                    console.log('Question:', $form.find('textarea[name="question_input"]').val()); // Debug log

                    try {
                        $submitBtn.prop('disabled', true).html('<span class="loading-spinner"></span> Submitting...');

                        const response = await axios.post($form.attr('action'), {
                            question_input: $form.find('textarea[name="question_input"]').val()
                        });

                        console.log('Response:', response.data); // Debug log

                        let message = '';
                        if (response.data == 1) {
                            message = '<div class="alert alert-success">Question Submitted Successfully!</div>';
                            $form[0].reset();
                            await showQuestion();
                        } else if (response.data == 2) {
                            message = '<div class="alert alert-warning">You have already submitted a question!</div>';
                        } else {
                            message = '<div class="alert alert-danger">Something went wrong!</div>';
                        }

                        $('#message').html(message).show();
                        setTimeout(() => $('#message').fadeOut(), 3000);

                    } catch (error) {
                        console.error('Question submission error:', error);
                        $('#message').html('<div class="alert alert-danger">Failed to submit question. Please try again.</div>').show();
                        setTimeout(() => $('#message').fadeOut(), 3000);
                    } finally {
                        $submitBtn.prop('disabled', false).html(originalText);
                    }
                    
                    return false; // Prevent default form submission
                }
            });
        } else {
            console.error('Question form not found!');
        }
    }

    async function showQuestion() {
        try {
            const response = await axios.post('{{ route("get-questions") }}', {
                request: 3
            });
            $('#messages').html(response.data);
        } catch (error) {
            console.error('Error loading questions:', error);
        }
    }

    // ==================== REACTION FUNCTIONS WITH RATE LIMITING ====================
    function initializeReactions() {
        $('#loveBtn, #likeBtn, #applause').on('click', function() {
            const reactionType = this.id === 'loveBtn' ? 'love' : (this.id === 'likeBtn' ? 'like' : 'applause');
            
            // Check if reaction already submitted in this session
            if (submittedReactions[reactionType]) {
                console.log(`You already submitted a ${reactionType} reaction in this session`);
                // Still show animation for visual feedback
                createReactionAnimation(reactionType);
                if (reactionType === 'applause') {
                    const audio = new Audio('{{ asset("assets/audio/audience-clapping.mp3") }}');
                    audio.play().catch(e => console.log('Audio play failed:', e));
                }
                return;
            }
            
            // Check rate limiting
            if (!reactionCooldown.canSubmit(reactionType)) {
                // Still show animation for visual feedback
                createReactionAnimation(reactionType);
                if (reactionType === 'applause') {
                    const audio = new Audio('{{ asset("assets/audio/audience-clapping.mp3") }}');
                    audio.play().catch(e => console.log('Audio play failed:', e));
                }
                return;
            }
            
            // Set cooldown
            reactionCooldown.setCooldown(reactionType);
            
            // Create animation and play sound
            createReactionAnimation(reactionType);
            if (reactionType === 'applause') {
                const audio = new Audio('{{ asset("assets/audio/audience-clapping.mp3") }}');
                audio.play().catch(e => console.log('Audio play failed:', e));
            }
            
            // Store in session to prevent duplicate database submissions
            submittedReactions[reactionType] = true;
            sessionStorage.setItem('submittedReactions', JSON.stringify(submittedReactions));
            
            // Send to server
            storeReaction(reactionType);
        });
    }

    function createReactionAnimation(type) {
        const heartContainer = document.getElementById('heartContainer');
        if (!heartContainer) return;
        
        const count = 30;
        for (let i = 0; i < count; i++) {
            const element = document.createElement('div');
            element.classList.add(type === 'love' ? 'heart' : (type === 'like' ? 'like' : 'clap'));
            element.style.left = `${Math.random() * 100}vw`;
            element.style.bottom = `-${Math.random() * 10}vh`;
            const size = Math.random() * 30 + 10;
            element.style.width = `${size}px`;
            element.style.height = `${size}px`;
            element.style.animationDuration = `${Math.random() * 4 + 4}s`;
            element.style.opacity = Math.random();
            element.style.position = 'fixed';
            element.style.zIndex = '9999';
            element.style.pointerEvents = 'none';

            heartContainer.appendChild(element);
            element.addEventListener('animationend', () => element.remove());
        }
    }

    async function storeReaction(reactionType) {
    try {
        const response = await axios.post('{{ route("store-reaction") }}', {
            reaction: reactionType
        });
        
        if (response.data.success) {
            console.log("Reaction recorded:", reactionType);
            // Mark as submitted in session
            submittedReactions[reactionType] = true;
            sessionStorage.setItem('submittedReactions', JSON.stringify(submittedReactions));
        }
    } catch (error) {
        console.error('Error storing reaction:', error);
        
        if (error.response && error.response.status === 429) {
            // Already submitted - mark as submitted in session
            submittedReactions[reactionType] = true;
            sessionStorage.setItem('submittedReactions', JSON.stringify(submittedReactions));
            console.log('Reaction already submitted:', reactionType);
        }
        // Silent fail - user sees only animation, no error messages
    }
}

    // ==================== USER ACTIVITY TRACKING (4 minute interval) ====================
    let activityInterval = null;
    let lastActivityTime = Date.now();

    function initializeActivityTracking() {
        // Track initial activity
        trackUserActivity();
        
        // Track activity every 4 minutes (240,000 ms) - matches 5 minute online threshold
        if (activityInterval) clearInterval(activityInterval);
        activityInterval = setInterval(trackUserActivity, 240000); // 4 minutes
        
        // Track activity on user interaction (debounced to reduce server load)
        let activityDebounceTimer;
        $(document).on('click keypress mousemove scroll', function() {
            const now = Date.now();
            // Only track if last activity was more than 30 seconds ago
            if (now - lastActivityTime > 30000) {
                lastActivityTime = now;
                if (activityDebounceTimer) clearTimeout(activityDebounceTimer);
                activityDebounceTimer = setTimeout(() => {
                    trackUserActivity();
                }, 1000);
            }
        });
    }

    async function trackUserActivity() {
        try {
            const response = await axios.post('{{ route("track-activity") }}');
            console.log('Activity tracked at:', new Date().toLocaleTimeString());
        } catch (error) {
            console.error('Error tracking activity:', error);
        }
    }

    // ==================== CLEANUP ====================
    window.addEventListener('beforeunload', function() {
        if (pollChannel) pollChannel.unbind();
        if (questionChannel) questionChannel.unbind();
        if (activityInterval) clearInterval(activityInterval);
    });
</script>
@endpush