import { HttpErrorResponse } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { MatSlideToggle } from '@angular/material/slide-toggle';
import { ActivatedRoute } from '@angular/router';
import { forkJoin, Observable } from 'rxjs';
import { PARTNER_SETTINGS } from 'src/app/shared/constants/app.constants';
import { PartnerSettingModel } from 'src/app/shared/db-models/partner-payment-account.model';
import { PartnerSettingService } from 'src/app/shared/feature-services/partner-setting.service';
import { AlertToastrService } from 'src/app/shared/util-services/alert-toastr.service';
import { CalioEventsService } from 'src/app/shared/util-services/calio-events.service';
import { LoggerService } from 'src/app/shared/util-services/logger.service';

@Component({
  selector: 'app-lexoffice-settings',
  templateUrl: './lexoffice-settings.component.html',
  styleUrls: ['./lexoffice-settings.component.scss']
})
export class LexofficeSettingsComponent implements OnInit {

  isLexOfficeEnabledSetting: PartnerSettingModel = new PartnerSettingModel();
  lexOfficeApiKeySetting: PartnerSettingModel = new PartnerSettingModel();
  isTCChecked = false;
  showLoader = false;
  submitted = false;
  disabled = false;

  constructor(
    private alertToastrService: AlertToastrService,
    private partnerSettingService: PartnerSettingService,
    private calioEventsService: CalioEventsService,
    private route: ActivatedRoute,
  ) {
    this.isLexOfficeEnabledSetting.value = 0;
  }

  ngOnInit(): void {
    this.calioEventsService.calioDashboardHeaderDataChanged.emit({
      title: undefined,
      subtitle: undefined,
      breadcrumbs: this.route.snapshot.data.breadcrumbs,
      permissionType: 'read:settings'
    });
    this.getPartnerSettings();
  }

  getPartnerSettings(): void {
    const settingIds = [
      PARTNER_SETTINGS.IS_LEXOFFICE_ENABLED,
      PARTNER_SETTINGS.LEXOFFICE_API_KEY
    ];

    this.partnerSettingService.getPartnerSettingsByIds(settingIds).subscribe({
      next: (partnerSettings: PartnerSettingModel[]) => {
        for (const partnerSetting of partnerSettings) {
          // is_lex_office_enabled
          if (partnerSetting.setting_id === 295) {
            this.isLexOfficeEnabledSetting = partnerSetting;
            if (this.isLexOfficeEnabledSetting.value) {
              this.isLexOfficeEnabledSetting.value = Number(this.isLexOfficeEnabledSetting.value);
              this.disabled = ((this.isLexOfficeEnabledSetting.value === 1) ? true : false);
            } else {
              if (this.isLexOfficeEnabledSetting.setting) {
                this.isLexOfficeEnabledSetting.value = Number(this.isLexOfficeEnabledSetting.setting.default_value);
              }
            }
          }
          // lex_office_api_key
          if (partnerSetting.setting_id === 296) {
            this.lexOfficeApiKeySetting = partnerSetting;
          }
        }
      }
    });
  }

  submit(): void {
    this.submitted = true;
    // setting id for creation purpose
    this.isLexOfficeEnabledSetting.setting_id = 295;
    this.lexOfficeApiKeySetting.setting_id = 296;

    const observables: Observable<any>[] = [];
    observables.push(this.partnerSettingService.createPartnerSettings(this.isLexOfficeEnabledSetting)); // is_lex_office_enabled
    observables.push(this.partnerSettingService.createPartnerSettings(this.lexOfficeApiKeySetting)); // lex_office_api_key

    forkJoin(observables).subscribe({
      next: (responseList: any[]) => {
        if (responseList && responseList.length > 0) {
          this.getPartnerSettings();
          this.alertToastrService.showSuccess('settings_component.settings_saved_successfully');
        } else {
          this.alertToastrService.showError('settings_component.settings_failed');
        }
      },
      error: (error: HttpErrorResponse) => {
        LoggerService.log('error is ', error);
        this.alertToastrService.showError('settings_component.settings_failed');
      },
      complete: () => {
        this.submitted = false;
      }
    });
  }

  paymentEnableChange(e: MatSlideToggle): void {
    if (!e.checked) {
      this.showLoader = true;
      const observables: Observable<any>[] = [];

      // is_lex_office_enabled
      if (this.isLexOfficeEnabledSetting?.id) {
        observables.push(this.partnerSettingService.deletePartnerSetting(this.isLexOfficeEnabledSetting.id));
      }
      // lex_office_api_key
      if (this.lexOfficeApiKeySetting?.id) {
        observables.push(this.partnerSettingService.deletePartnerSetting(this.lexOfficeApiKeySetting.id));
      }

      forkJoin(observables).subscribe({
        next: (responseList: any[]) => {
          if (responseList && responseList.length > 0) {
            this.isTCChecked = false;
            this.disabled = false;
            const defaultSettings = {
              toggle: Number(this.isLexOfficeEnabledSetting.setting.default_value)
            };
            this.isLexOfficeEnabledSetting = new PartnerSettingModel();
            this.lexOfficeApiKeySetting = new PartnerSettingModel();

            this.isLexOfficeEnabledSetting.value = defaultSettings.toggle;

            this.alertToastrService.showSuccess('settings_component.settings_saved_successfully');
          } else {
            this.alertToastrService.showError('settings_component.settings_failed');
          }
        },
        error: (error: HttpErrorResponse) => {
          LoggerService.log('error is ', error);
          this.alertToastrService.showError('settings_component.settings_failed');
        },
        complete: () => {
          this.showLoader = false;
        }
      });
    }
  }
}
