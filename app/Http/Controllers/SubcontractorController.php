<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubcontractorRequest;
use App\Models\Site;
use App\Models\Subcontractor;
use App\Support\SessionAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SubcontractorController extends Controller
{
    public function index(Request $request, SessionAuth $auth): View
    {
        abort_unless($auth->hseUser()?->isAdmin() ?? false, Response::HTTP_FORBIDDEN);

        $site = $request->string('site', '')->toString() ?: null;
        $search = trim($request->string('q', '')->toString());

        $subcontractors = Subcontractor::query()
            ->with('site')
            ->when($site !== null, fn ($q) => $q->forSite($site))
            ->when($search !== '', fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('bidang', 'like', "%{$search}%"))
            ->orderBy('site_code')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('subkon.index', [
            'subcontractors' => $subcontractors,
            'sites' => Site::orderBy('code')->get(),
            'site' => $site,
            'search' => $search,
        ]);
    }

    public function create(SessionAuth $auth): View
    {
        abort_unless($auth->hseUser()?->isAdmin() ?? false, Response::HTTP_FORBIDDEN);

        return view('subkon.create', [
            'sites' => Site::orderBy('code')->get(),
        ]);
    }

    public function store(StoreSubcontractorRequest $request): RedirectResponse
    {
        $sub = Subcontractor::create($request->validated());

        return redirect()->route('subkon.index')->with('ok', "Data {$sub->nama} berhasil ditambahkan.");
    }

    public function destroy(Subcontractor $subcontractor, SessionAuth $auth): RedirectResponse
    {
        abort_unless($auth->hseUser()?->isAdmin() ?? false, Response::HTTP_FORBIDDEN);

        $name = $subcontractor->nama;
        $subcontractor->delete();

        return redirect()->route('subkon.index')->with('ok', "Data {$name} berhasil dihapus.");
    }
}
