<ul class="nav nav-pills flex-column text-center text-md-left">

    <li class="nav-item">
{{--        {{dd($watchman_id)}}--}}
        <a class="nav-link {{ (request()->is('trainer-info*')) ? 'active' : '' }}" id="stacked-pill-1"  @if(isset($trainer_id)) href="{{route('trainer-information.trainer-info-get',['trainer_id'=>$trainer_id])}}" @else href="{{route('trainer-information.trainer-info-get')}}" @endif
           aria-expanded="true"><i class="bx bx-info-circle"></i>
            <span> Basic Info</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (request()->is('trainer-address*')) ? 'active' : '' }}" id="stacked-pill-2"  href="{{route('trainer-information.trainer-address-get',$trainer_id)}}"
           aria-expanded="false"><i class="bx bx-map"></i>
            <span>Address</span>
        </a>
    </li>
    <li class="nav-item">
         <a class="nav-link {{ (request()->is('trainer-education*')) ? 'active' : '' }}" id="stacked-pill-3"  href="{{route('trainer-information.trainer-education-get',$trainer_id)}}"
            aria-expanded="false"><i class="bx bx-highlight"></i>
             <span>Education</span>
         </a>
     </li>
   <li class="nav-item">
        <a class="nav-link {{ (request()->is('trainer-experience*')) ? 'active' : '' }}" id="stacked-pill-4"  href="{{route('trainer-information.trainer-experience-get',$trainer_id)}}"
           aria-expanded="false"><i class="bx bxs-file"></i>
            <span>Experience</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (request()->is('trainer-training*')) ? 'active' : '' }}" id="stacked-pill-4"  href="{{route('trainer-information.trainer-training-get',$trainer_id)}}"
           aria-expanded="false"><i class="bx bxs-bank"></i>
            <span>Training</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (request()->is('trainer-expertise*')) ? 'active' : '' }}" id="stacked-pill-4"  href="{{route('trainer-information.trainer-expertise-get',$trainer_id)}}"
           aria-expanded="false"><i class="bx bx-sitemap"></i>
            <span>Expertise</span>
        </a>
    </li>
</ul>
