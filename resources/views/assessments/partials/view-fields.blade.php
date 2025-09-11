<style>
    thead th {
      position: sticky;
      top: 0;
      color: #fff;
      z-index: 2; 
    }
    thead th:first-child {
        min-width: 70px;
    }

    thead th {
    background: #198754 !important;
    color: #fff !important;
    }

    table {
      border-collapse: separate;
      border-spacing: 0;
    }
    table td {
        vertical-align: top;
    }

    .list-alpha {
      list-style-type: lower-alpha; 
      padding-left: 1.5rem; 
    }

    .list-alpha li {
      margin-bottom: 0.5rem;
    }
    textarea.form-control {
        min-height: 100px;
        resize: none;
    }
  </style>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead>
          <tr class="bg-success text-white">
            <th>Sr. No.</th>
            <th>Performance Description</th>
            <th>Details</th>
            <th class="w-25">Comment</th>
          </tr>
        </thead>
        <tbody>

          <tr>
            <td>1</td>
            <td>Regularity | Availability</td>
            <td>
                <ol class="list-alpha">
                    <li>About Frequent Leaves</li>
                    <li>On time Office</li>
                    <li>Ready to sit for Extra time, if there is any requirement</li>
                    <li>How ready to work on weekends</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_1"} ?? '' }}</td>
          </tr>
          <tr>
            <td>2</td>
            <td>Attitude</td>
            <td>
                <ol class="list-alpha">
                    <li>Towards Work</li>
                    <li>Towards Colleagues</li>
                    <li>Towards Learning new technology / Frameworks | Willingness to learn new technology?</li>
                    <li>Are you Self -Motivated ?</li>
                    <li>Do you think you are loyal, Honest & Follow Company's policy & security?</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_2"} ?? '' }}</td>
          </tr>
          <tr>
            <td>3</td>
            <td>Taking Responsibility	</td>
            <td>
                <ol class="list-alpha">
                    <li>How do you justify yourself that you are a responsible person?</li>
                    <li>How ready you are to take the responsibility?</li>
                    <li>Do you believe that you are the responsible person for your own task?</li>
                    <li>What about the project responsiblity | task responsibility ?</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_3"} ?? '' }}</td>
          </tr>

    
          <tr>
            <td>4</td>
            <td>Requirement Understanding	</td>
            <td>
                <ol class="list-alpha">
                    <li>Able to understand requirement properly?</li>
                    <li>Is the solution provided by you as per the requirement?</li>
                    <li>Need to explain the same requirement again and again?</li>
                    <li>How you keep the requirement track given on skype call?</li>
                    <li>Do you do any homework, once requirement will be given completely? or what is your approach towards the development?</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_4"} ?? '' }}</td>
          </tr>
          <tr>
            <td>5</td>
            <td>Problem Solving skills</td>
            <td>
                <ol class="list-alpha">
                    <li>How you are good at with problem solving skill?</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_5"} ?? '' }}</td>
          </tr>
          <tr>
            <td>6</td>
            <td>Being a good team member</td>
            <td>
                <ol class="list-alpha">
                    <li>Yes / No</li>
                    <li>Your involvement as team member</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_6"} ?? '' }}</td>
          </tr>
          <tr>
            <td>7</td>
            <td>Task Management / Record Management</td>
            <td>
                <ol class="list-alpha">
                    <li>Did you manage your tasks as per the instructions?</li>
                    <li>Do you manage Asana properly? like updates / Comments / managing timer</li>
                    <li>How do you keep server credentials (Staging / Production) ? Are those up to date and secured?</li>
                    <li>About clickup management</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_7"} ?? '' }}</td>
          </tr>
          <tr>
            <td>8</td>
            <td>Git hub repository</td>
             <td>
                <ol class="list-alpha">
                    <li>Able to manage Git repository properly and as per the given instructions</li>
                    <li>Able to solve code conflicts or anything</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_8"} ?? '' }}</td>
          </tr>
          <tr>
            <td>9</td>
            <td>Code Quality</td>
            <td>
                <ol class="list-alpha">
                    <li>Optimized code</li>
                    <li>Following Code standards like Class based code structure | Proper Comments | Optimized queries (For Backend Developer)</li>
                    <li>For Frontend Developer, are they good at in defining classes and optimized CSS? Are they good at with Browser based javascripts? Able to deliver the solution as per the requirement?</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_9"} ?? '' }}</td>
          </tr>

          <tr>
            <td>10</td>
            <td>Testing</td>
            <td>
                <ol class="list-alpha">
                    <li>Justify yourself for testing skills</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_10"} ?? '' }}</td>
          </tr>

          <tr>
            <td>11</td>
            <td>On time delivery	</td>
            <td>
                <ol class="list-alpha">
                    <li>Delivered solutions ontime ? Justify	</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_11"} ?? '' }}</td>
          </tr>

          <tr>
            <td>12</td>
            <td>Learning any new technology	</td>
            <td>
                <ol class="list-alpha">
                    <li>Justify yourself	</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_12"} ?? '' }}</td>
          </tr>

          <tr>
            <td>13</td>
            <td>Communication Skill		</td>
            <td>
                <ol class="list-alpha">
                    <li>Any comments about the Communication Skills	</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_13"} ?? '' }}</td>
          </tr>

          <tr>
            <td>14</td>
            <td>Your Long term goal	</td>
            <td>
                <ol class="list-alpha">
                    <li>To define your role with AIS Pvt Ltd.	</li>
                </ol>
            </td>
            <td>{{ $assessment->{"comment_14"} ?? '' }}</td>
          </tr>

          <tr>
            <td>15</td>
            <td>Work from Home / Work from Office	</td>
            <td>
               &nbsp;
            </td>
            <td>{{ $assessment->{"comment_15"} ?? '' }}</td>
          </tr>
		  
		  <tr>
            <td style="border-bottom: 1px solid #e5e7eb;" colspan="2">Final Conclusion</td>
            <td style="border-bottom: 1px solid #e5e7eb;" colspan="2">{{ $assessment->{"final_conclusion"} ?? '' }}</td>
          </tr>
          
        </tbody>
      </table>
    </div>