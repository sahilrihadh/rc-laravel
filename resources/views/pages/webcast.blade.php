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

    .toast-notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
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

    // ==================== PUSHER SETUP (Better than PubNub) ====================
    const pusher = new Pusher('aca7938088f631ee68de', {
        cluster: 'ap2',
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        }
    });


    // In your webcast.blade.php
    const pollChannel = pusher.subscribe('poll-channel');

    pollChannel.bind('poll-status-changed', function(data) {
        console.log('Poll status changed:', data);

        if (data.status === 'active' && data.pollHtml) {
            // Show poll
            $('#poll').html(data.pollHtml);
            $('#pollSidebarCollapse').click();
            showToast('New poll available! Click to participate.', 'info');
        } else if (data.status === 'inactive') {
            // Hide poll
            $('#pollSidebar').removeClass('active');
            $('#poll').html('<div class="alert alert-info">No active poll available at the moment.</div>');
            showToast('Poll has ended. Thank you for participating!', 'info');
        }
    });

    // Subscribe to channels
    const pollChannel = pusher.subscribe('poll-channel');
    const questionChannel = pusher.subscribe('question-channel');

    // ==================== STATE MANAGEMENT ====================
    let pollRefreshInterval = null;
    let questionRefreshInterval = null;
    let isPollActive = false;

    // ==================== INITIALIZATION ====================
    $(document).ready(function() {
        initializeSidebars();
        initializePoll();
        initializeQuestions();
        initializeReactions();
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

    // ==================== POLL FUNCTIONS (Using Pusher) ====================
    function initializePoll() {
        // Listen for poll status changes via Pusher
        pollChannel.bind('poll-status-changed', function(data) {
            console.log('Poll status changed:', data);
            if (data.status === 'active') {
                checkPoll();
            } else if (data.status === 'inactive') {
                closePoll();
            }
        });

        // Initial poll check
        checkPoll();
    }

    async function checkPoll() {
        try {
            const response = await axios.post('{{ route("check-poll") }}', {
                request: 1
            });

            if (response.data === 'NO_POLL_ACTIVE' || response.data === '') {
                closePoll();
            } else {
                showPoll(response.data);
            }
        } catch (error) {
            console.error('Error checking poll:', error);
        }
    }

    function showPoll(pollHtml) {
        $('#pollSidebarCollapse').click();
        $('#poll').html(pollHtml);
        isPollActive = true;
    }

    function closePoll() {
        $("#pollSidebar").removeClass("active");
        $('#poll').html('<div class="alert alert-info">No active poll available at the moment.</div>');
        isPollActive = false;
    }

    // Submit vote using Axios
    $(document).on("click", "#but_vote", async function(e) {
        e.preventDefault();

        const checkedPoll = $("#poll input[name='poll']:checked").val();

        if (!checkedPoll) {
            showToast('Please select an option.', 'warning');
            return;
        }

        const $btn = $(this);
        const originalText = $btn.text();

        try {
            $btn.prop('disabled', true).text('Submitting...');

            const response = await axios.post('{{ route("submit-vote") }}', {
                request: 2,
                poll: checkedPoll
            });

            if (response.data == 1) {
                showToast('Vote submitted successfully!', 'success');
                await checkPoll();
            } else {
                showToast('Failed to submit vote. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Vote submission error:', error);
            showToast('An error occurred. Please try again.', 'error');
        } finally {
            $btn.prop('disabled', false).text(originalText);
        }
    });

    // ==================== QUESTION FUNCTIONS ====================
    function initializeQuestions() {
        // Listen for new questions via Pusher
        questionChannel.bind('new-question', function(data) {
            console.log('New question received:', data);
            showQuestion(); // Refresh question list
            showToast('New question has been posted!', 'info');
        });

        // Initial question load
        showQuestion();

        // Refresh questions every 30 seconds as fallback
        if (questionRefreshInterval) clearInterval(questionRefreshInterval);
        questionRefreshInterval = setInterval(showQuestion, 30000);
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

    // Question form validation and submission
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
        submitHandler: async function(form) {
            const $form = $(form);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();
            const questionText = $form.find('textarea[name="question_input"]').val();

            try {
                $submitBtn.prop('disabled', true).html('<span class="loading-spinner"></span> Submitting...');

                const response = await axios.post($form.attr('action'), {
                    question_input: questionText
                });

                let message = '';
                if (response.data == 1) {
                    message = '<div class="alert alert-success">Question Submitted Successfully!</div>';
                    $form[0].reset();
                    await showQuestion(); // Refresh questions immediately
                    showToast('Your question has been submitted!', 'success');
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
                $submitBtn.prop('disabled', false).text(originalText);
            }
        }
    });

    // ==================== REACTION FUNCTIONS ====================
    function initializeReactions() {
        $('#loveBtn, #likeBtn, #applause').on('click', function() {
            const reactionType = this.id === 'loveBtn' ? 'love' : (this.id === 'likeBtn' ? 'like' : 'applause');
            createReactionAnimation(reactionType);
            storeReaction(reactionType);

            if (reactionType === 'applause') {
                const audio = new Audio('{{ asset("assets/audio/audience-clapping.mp3") }}');
                audio.play().catch(e => console.log('Audio play failed:', e));
            }
        });
    }

    function createReactionAnimation(type) {
        const heartContainer = document.getElementById('heartContainer');
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
            await axios.post('{{ route("store-reaction") }}', {
                reaction: reactionType
            });
        } catch (error) {
            console.error('Error storing reaction:', error);
        }
    }

    // ==================== UTILITY FUNCTIONS ====================
    function showToast(message, type = 'info') {
        const toastHtml = `
            <div class="toast-notification alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show" role="alert">
                <strong>${type === 'error' ? 'Error!' : type === 'success' ? 'Success!' : 'Notice!'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        $('body').append(toastHtml);

        setTimeout(() => {
            $('.toast-notification').fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // ==================== CLEANUP ====================
    window.addEventListener('beforeunload', function() {
        if (pollRefreshInterval) clearInterval(pollRefreshInterval);
        if (questionRefreshInterval) clearInterval(questionRefreshInterval);

        // Unsubscribe from Pusher channels
        if (pollChannel) pollChannel.unbind();
        if (questionChannel) questionChannel.unbind();
    });
</script>
@endpush