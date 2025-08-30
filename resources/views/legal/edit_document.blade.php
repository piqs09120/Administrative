@csrf
@method('PUT')

<div class="space-y-6">
    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">Document Title *</span>
        </label>
        <input type="text" name="title" id="editTitle" class="input input-bordered w-full" required>
    </div>

    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">Category</span>
        </label>
        <select name="category" id="editCategory" class="select select-bordered w-full">
            <option value="contract">Contract</option>
            <option value="legal_notice">Legal Notice</option>
            <option value="policy">Policy</option>
            <option value="compliance">Compliance</option>
            <option value="financial">Financial</option>
            <option value="report">Report</option>
            <option value="memorandum">Memorandum</option>
            <option value="affidavit">Affidavit</option>
            <option value="subpoena">Subpoena</option>
            <option value="cease_desist">Cease & Disist</option>
            <option value="legal_brief">Legal Brief</option>
        </select>
    </div>

    <div class="form-control">
        <label class="label">
            <span class="label-text font-semibold">Description</span>
        </label>
        <textarea name="description" id="editDescription" class="textarea textarea-bordered w-full h-32"></textarea>
    </div>
</div>

