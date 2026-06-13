<?php $__env->startSection('title', 'Create Poll'); ?>
<?php $__env->startSection('page-title', 'Create New Poll'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('admin.polls.index')); ?>">Polls</a></li>
<li class="breadcrumb-item active">Create</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card" x-data="pollForm()">
  <div class="card-header">
    <h5 class="mb-0">Create Poll</h5>
    <a href="<?php echo e(route('admin.polls.index')); ?>" class="btn btn-secondary btn-sm float-end">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>
  <div class="card-body">
    <?php if($errors->any()): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.polls.store')); ?>">
      <?php echo csrf_field(); ?>

      <div class="mb-3">
        <label class="form-label">Poll Question</label>
        <textarea name="question" class="form-control <?php $__errorArgs = ['question'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" required placeholder="Enter your poll question..."><?php echo e(old('question')); ?></textarea>
        <?php $__errorArgs = ['question'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div class="mb-3">
        <label class="form-label">Options (Minimum 2) - Select the correct answer</label>
        <div id="options-container">
          <template x-for="(option, index) in options" :key="index">
            <div class="input-group mb-2 option-group">
              <div class="input-group-text">
                <input type="radio" name="correct_option" :value="index" class="form-check-input mt-0" :required="options.length > 0">
              </div>
              <input type="text" 
                     name="options[]" 
                     class="form-control" 
                     :placeholder="'Option ' + (index + 1)" 
                     x-model="options[index]"
                     required>
              <button type="button" class="btn btn-danger remove-option" x-show="options.length > 2" @click="removeOption(index)">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </template>
        </div>
        <button type="button" class="btn btn-sm btn-success mt-2" @click="addOption">
          <i class="fas fa-plus"></i> Add Option
        </button>
        <div class="form-text text-muted mt-2">
          <i class="fas fa-info-circle"></i> Select the radio button next to the correct answer
        </div>
        <?php $__errorArgs = ['options'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="text-danger mt-1"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <?php $__errorArgs = ['correct_option'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="text-danger mt-1"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      

      <button type="submit" class="btn btn-primary">Create Poll</button>
    </form>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
  function pollForm() {
    return {
      options: <?php echo json_encode(old('options', ['', ''])) ?>,
      
      addOption() {
        this.options.push('');
      },
      
      removeOption(index) {
        if (this.options.length > 2) {
          this.options.splice(index, 1);
        }
      }
    }
  }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sahilrihadh/Development/royalcanin/webinar-solution/resources/views/admin/polls/create.blade.php ENDPATH**/ ?>