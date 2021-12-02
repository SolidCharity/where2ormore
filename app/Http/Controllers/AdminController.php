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

    // count how many participants have declared 2G
    static private function calc2GforServices(&$services, &$participants)
    {
        foreach ($services as $service)
        {
            $service->have_no_2g = $service->count_adults;
            $service->have_2g = 0;
            foreach($participants as $participant)
            {
                if ($participant->service_id == $service->id and $participant->all_have_2g)
                {
                    $service->have_2g += $participant->count_adults;
                    $service->have_no_2g -= $participant->count_adults;
                    $participant->have_all_2g_msg = "2G";
                }
            }
        }
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
        $option_for_separate_firstname_checked = ($tenant->option_for_separate_firstname?"checked":"");
        $option_to_declare_2g_checked = ($tenant->option_to_declare_2g?"checked":"");
        $option_for_3g_signatures_checked = ($tenant->option_for_3g_signatures?"checked":"");
        $option_for_single_registration_checked = ($tenant->option_for_single_registration?"checked":"");
        $text_for_signup_for_closed_event = $tenant->text_for_signup_for_closed_event;
        if ($text_for_signup_for_closed_event == 'error_registration_closed') {
            $text_for_signup_for_closed_event = __('messages.error_registration_closed');
        }
        $text_for_3g_rules_description = $tenant->text_for_3g_rules_description;
        $text_for_report_church_details = $tenant->text_for_report_church_details;
        $text_for_report_welcome_person = $tenant->text_for_report_welcome_person;
        $text_for_report_destroy_list = $tenant->text_for_report_destroy_list;

        self::calc2GforServices($services, $participants);

        return view('admin', ['services' => $services,
            'participants' => $participants, 'link_visitors' => $visitor_link, 'churchname' => $churchname,
            'collect_contact_details_checked' => $collect_contact_details_checked,
            'option_for_separate_firstname_checked' => $option_for_separate_firstname_checked,
            'option_to_declare_2g_checked' => $option_to_declare_2g_checked,
            'option_for_3g_signatures_checked' => $option_for_3g_signatures_checked,
            'option_for_single_registration_checked' => $option_for_single_registration_checked,
            'option_to_report_contact_details_checked' => $option_to_report_contact_details_checked,
            'text_for_signup_for_closed_event' => $text_for_signup_for_closed_event,
            'text_for_3g_rules_description' => $text_for_3g_rules_description,
            'text_for_report_church_details' => $text_for_report_church_details,
            'text_for_report_welcome_person' => $text_for_report_welcome_person,
            'text_for_report_destroy_list' => $text_for_report_destroy_list,
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
            $participants = \App\Participant::where('tenant_id', $tenant_id)->orderBy('name')->get()->sortBy('firstname', SORT_NATURAL|SORT_FLAG_CASE)->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE);
            $services = \App\Service::where('tenant_id', $tenant_id)->get();
        } else {
            $participants = \App\Participant::where([['tenant_id', $tenant_id],['service_id',$service_id]])->orderBy('name')->get()->sortBy('firstname', SORT_NATURAL|SORT_FLAG_CASE)->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE);
            $services = \App\Service::where([['tenant_id', $tenant_id],['id',$service_id]])->get();
        }

        self::calc2GforServices($services, $participants);

        return view('report', ['services' => $services,
            'participants' => $participants,
            'tenant' => $tenant,
            'collect_contact_details' => $tenant->collect_contact_details,
            'option_for_single_registration' => $tenant->option_for_single_registration || $tenant->option_for_3g_signatures,
            'display_2g' => $tenant->option_to_declare_2g,
            'display_3g_status' => $tenant->option_for_3g_signatures,
            'display_signatures' => $tenant->option_for_3g_signatures || $tenant->option_to_declare_2g]);
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

    /// update the details of the current tenant
    public function updateTenantDetails(Request $request)
    {
        $tenant_id = Auth::user()->tenant_id;

        $data = $request->validate([
            'churchname' => 'nullable|string',
            'collect_contact_details' => 'boolean',
            'option_to_report_contact_details' => 'boolean',
            'option_for_separate_firstname' => 'boolean',
            'option_to_declare_2g' => 'boolean',
            'option_for_3g_signatures' => 'boolean',
            'option_for_single_registration' => 'boolean',
            'text_for_signup_for_closed_event' => 'nullable|string|max:250',
            'text_for_3g_rules_description' => 'nullable|string|max:250',
            'text_for_report_welcome_person' => 'nullable|string|max:250',
            'text_for_report_destroy_list' => 'nullable|string|max:250',
            'text_for_report_church_details' => 'nullable|string|max:250',
            ],[
            'text_for_signup_for_closed_event:max' => 'The text must not be longer than 190 characters',
            'text_for_3g_rules_description:max' => 'The text must not be longer than 190 characters',
            'text_for_report_welcome_person:max' => 'The text must not be longer than 190 characters',
            'text_for_report_destroy_list:max' => 'The text must not be longer than 190 characters',
            'text_for_report_church_details:max' => 'The text must not be longer than 190 characters',
        ]);

        if (empty($data['churchname'])) {
            $data['churchname'] = '';
        }
        if (empty($data['collect_contact_details'])) {
            $data['collect_contact_details'] = '0';
        }
        if (empty($data['option_to_report_contact_details'])) {
            $data['option_to_report_contact_details'] = '0';
        }
        if (empty($data['option_for_separate_firstname'])) {
            $data['option_for_separate_firstname'] = '0';
        }
        if (empty($data['option_to_declare_2g'])) {
            $data['option_to_declare_2g'] = '0';
        }
        if (empty($data['option_for_3g_signatures'])) {
            $data['option_for_3g_signatures'] = '0';
        }
        if (empty($data['option_for_single_registration'])) {
            $data['option_for_single_registration'] = '0';
        }
        if ($data['text_for_signup_for_closed_event'] == '' ||
            $data['text_for_signup_for_closed_event'] == __('messages.error_registration_closed')) {
            $data['text_for_signup_for_closed_event'] = 'error_registration_closed';
        } else {
            // strip any html tags
            $data['text_for_signup_for_closed_event'] = str_replace('<','&lt;',$data['text_for_signup_for_closed_event']);
        }
        $data['text_for_3g_rules_description'] = str_replace('<','&lt;',$data['text_for_3g_rules_description']);

        $tenant = \App\Tenant::
            where('id',$tenant_id)->first();
        $tenant->name = $data['churchname'];
        $tenant->collect_contact_details = $data['collect_contact_details'];
        $tenant->option_to_report_contact_details = $data['option_to_report_contact_details'];
        $tenant->option_for_separate_firstname = $data['option_for_separate_firstname'];
        $tenant->option_to_declare_2g = $data['option_to_declare_2g'];
        $tenant->option_for_3g_signatures = $data['option_for_3g_signatures'];
        $tenant->option_for_single_registration = $data['option_for_single_registration'];
        $tenant->text_for_signup_for_closed_event = $data['text_for_signup_for_closed_event'];
        $tenant->text_for_3g_rules_description = $data['text_for_3g_rules_description'];
        $tenant->text_for_report_welcome_person = $data['text_for_report_welcome_person'];
        $tenant->text_for_report_destroy_list = $data['text_for_report_destroy_list'];
        $tenant->text_for_report_church_details = $data['text_for_report_church_details'];
        $tenant->save();

        return redirect('/admin#settings');
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
