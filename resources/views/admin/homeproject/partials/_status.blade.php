<form method="get" action="{{ route('ambulance.change-status', $data->id) }}" class="d-inline" onsubmit="return confirmStatusChange(this)">
  @csrf
  <button type="submit" class="btn btn-link">
      <i class="{{ $data->status == 'Y' ? 'fal fa-check' : 'fal fa-backspace' }}"></i>
  </button>
</form>
