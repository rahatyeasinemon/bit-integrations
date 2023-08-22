const changelogInfo = {
    '2.2.0': {
      date: '15-July-2023',
      changes: {
        added: {
          label: 'Added',
          list: [
            {
              label: 'New Field',
              tag: 'new',
              list: [
                'Repeater Field - to create repeated forms with complex conditional logics calculation',
              ],
            },
            {
              label: 'Features',
              list: [
                {
                  label: 'Form Abandonment or Partial Form Submission',
                  tag: 'new',
                },
                {
                  label: 'Pdf Attachment of Form Entry in Email Notifications',
                  tag: 'new',
                },
              ],
            },
          ],
        },
        fixed: {
          label: 'Fixed',
          list: [
            'Form entry edit route 404 issue',
            "Forms not initializing on Elementor page builder's modal",
            'Telegram Integration issue regarding multiple uploaded files',
            'MailerLite Integration timeline log issue',
            'Zoho workdrive integration next button disabled issue',
            'Country field default value issue from Conditional Logics',
            'bitform_dequeue_styles hook issue with form ids',
            'Deprycated notice from shortcode function if form content is empty',
            'alt attribute not updating from custom attribute settings',
          ],
        },
      },
    },
    '2.3.0': {
      date: '01 August, 2023',
      changes: {
        added: {
          label: 'Added',
          list: [
            {
              label: 'New Field',
              tag: 'new',
              list: [
                'Signature Field - Collect signatures from your wp users to store in DB as PNG, JPG or SVG and use it in the Email or PDF templates.',
              ],
            },
            {
              label: 'Features',
              list: [
                {
                  label: 'Piping - Use the form field values as the Label, Sub title & Helper text of other form fields via our advanced conditional logics to make your form more dynamic.',
                  tag: 'new',
                },
              ],
            },
          ],
        },
        fixed: {
          label: 'Fixed',
          list: [
            'Phone number field validation message issue of validate on focus out option',
            'Razorpay payment field triggers the form submit actions before the payment is completed',
            'Form is not saving by giving the error of Form is empty when the form has only the section fields (rather it has fields inside the section field)',
            'Dropdown field not selecting the options when user is not logged in because of the Comet Cache plugin',
          ],
        },
      },
    },
  }
  
  export default changelogInfo
  