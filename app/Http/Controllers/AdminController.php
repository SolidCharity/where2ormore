<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $tenant_id = Auth::user()->tenant_id;

        // get all services
        $services = \App\Service::where('tenant_id', $tenant_id)->get();
        $participants = \App\Participant::where('tenant_id', $tenant_id)->get();
        $tenant = \DB::table('tenants')->where('id', $tenant_id)->first();

        if (!empty($tenant->external_url))
        {
            $visitor_link = $tenant->external_url;
        }
        else
        {
            $visitor_link = $request->getSchemeAndHttpHost();
            // this is not a single instance installation
            if (\DB::table('users')->where('tenant_id', "=", 1)->count() == 0)
            {
                $visitor_link .= "/?uuid=".$tenant->uuid;
            }
        }

        $churchname = $tenant->name;
        $collect_contact_details_checked = ($tenant->collect_contact_details?"checked":"");
        $option_to_report_contact_details_checked = ($tenant->option_to_report_contact_details?"checked":"");
        $text_for_signup_for_closed_event = $tenant->text_for_signup_for_closed_event;
        if ($text_for_signup_for_closed_event == 'error_registration_closed') {
            $text_for_signup_for_closed_event = __('messages.error_registration_closed');
        }

        return view('admin', ['services' => $services,
            'participants' => $participants, 'link_visitors' => $visitor_link, 'churchname' => $churchname,
            'collect_contact_details_checked' => $collect_contact_details_checked,
            'option_to_report_contact_details_checked' => $option_to_report_contact_details_checked,
            'text_for_signup_for_closed_event' => $text_for_signup_for_closed_event,
            ]);
    }

    /// print a report with the visitors for each service
    static public function report($service_id)
    {
        if (empty(Auth::user())) {
            return redirect('/login');
        }

        $tenant_id = Auth::user()->tenant_id;
        $tenant = \App\Tenant::
            where('id',$tenant_id)->first();

        if (empty($service_id)) {
            $participants = \App\Participant::where('tenant_id', $tenant_id)->get();
            $services = \App\Service::where('tenant_id', $tenant_id)->get();
        } else {
            $participants = \App\Participant::where([['tenant_id', $tenant_id],['service_id',$service_id]])->get();
            $services = \App\Service::where([['tenant_id', $tenant_id],['id',$service_id]])->get();
        }

        return view('report', ['services' => $services,
            'participants' => $participants,
            'collect_contact_details' => $tenant->collect_contact_details]);
    }

    /// drop all participants, as preparation for next week's Sunday!
    static public function dropAllParticipants($service_id)
    {
        if (empty(Auth::user())) {
            return redirect('/login');
        }

        $tenant_id = Auth::user()->tenant_id;

        if (empty($service_id)) {
            $participants = \App\Participant::where('tenant_id', $tenant_id)->get();
        } else {
            $participants = \App\Participant::where([['tenant_id', $tenant_id],['service_id',$service_id]])->get();
        }

        foreach ($participants as $participant)
        {
            $participant->delete();
        }

        return redirect('/admin');
    }

    /// update the name of the current tenant
    public function updateChurchname(Request $request)
    {
        $tenant_id = Auth::user()->tenant_id;

        $data = $request->validate([
            'churchname' => 'nullable|string',
        ]);

        if (empty($data['churchname'])) {
		$data = array('churchname' => '');
        }

        $tenant = \App\Tenant::
            where('id',$tenant_id)->first();
        $tenant->name = $data['churchname'];
        $tenant->save();

        return redirect('/admin');
    }

    /// update the flag to save contact details for this tenant
    public function updateCollectContactDetails(Request $request)
    {
        $tenant_id = Auth::user()->tenant_id;

        $data = $request->validate([
            'collect_contact_details' => 'boolean',
        ]);

        if (empty($data['collect_contact_details'])) {
		$data = array('collect_contact_details' => '0');
        }

        $tenant = \App\Tenant::
            where('id',$tenant_id)->first();
        $tenant->collect_contact_details = $data['collect_contact_details'];
        $tenant->save();

        return redirect('/admin');
    }

    /// update the flag to allow the option to include contact details on the report for this tenant
    public function updateOptionToReportContactDetails(Request $request)
    {
        $tenant_id = Auth::user()->tenant_id;

        $data = $request->validate([
            'option_to_report_contact_details' => 'boolean',
        ]);

        if (empty($data['option_to_report_contact_details'])) {
		$data = array('option_to_report_contact_details' => '0');
        }

        $tenant = \App\Tenant::
            where('id',$tenant_id)->first();
        $tenant->option_to_report_contact_details = $data['option_to_report_contact_details'];
        $tenant->save();

        return redirect('/admin');
    }

    /// update the text that is displayed if someone tries to signup for an event with closed registration
    public function updateTextForSignupForClosedEvent(Request $request)
    {
        $tenant_id = Auth::user()->tenant_id;

        $data = $request->validate([
            'text_for_signup_for_closed_event' => 'nullable|string|max:190',
        ],[
            'text_for_signup_for_closed_event:max' => 'The text must not be longer than 190 characters',
        ]);

        if ($data['text_for_signup_for_closed_event'] == '' ||
            $data['text_for_signup_for_closed_event'] == __('messages.error_registration_closed')) {
            $data['text_for_signup_for_closed_event'] = 'error_registration_closed';
        } else {
            // strip any html tags
            $data['text_for_signup_for_closed_event'] = str_replace('<','&lt;',$data['text_for_signup_for_closed_event']);
        }

        $tenant = \App\Tenant::
            where('id',$tenant_id)->first();
        $tenant->text_for_signup_for_closed_event = $data['text_for_signup_for_closed_event'];
        $tenant->save();

        return redirect('/admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminHome()
    {
        return view('adminHome');
    }
}
