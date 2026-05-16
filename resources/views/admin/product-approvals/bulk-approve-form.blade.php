<form method="POST" id="bulk-approve-form" action="{{ route('admin.products.bulk') }}" hidden>
    @csrf
    @method('PATCH')
    <input type="hidden" name="action" value="approve">
    <div id="bulk-approve-ids"></div>
</form>
