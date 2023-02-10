import {Component, OnInit} from '@angular/core';
import {OnlinePaymentService} from '../../../../shared/feature-services/online-payment.service';
import {PartnerDbModel} from '../../../../shared/db-models/partner-db.model';
import {SubscriptionModel} from '../../../../shared/db-models/subscribe.model';
import {Auth0Service} from '../../../../shared/feature-services/auth-0.service';
import {OnlinePaymentModel} from '../../../../shared/db-models/online-payment.model';
import {APP_CONSTANTS, PARTNER_SETTINGS} from '../../../../shared/constants/app.constants';
import {WorkerDbModel} from '../../../../shared/db-models/worker-db.model';

@Component({
  selector: 'app-navigation-items-card',
  templateUrl: './navigation-items-card.component.html',
  styleUrls: ['./navigation-items-card.component.scss']
})
export class NavigationItemsCardComponent implements OnInit {

  readonly appConstants = APP_CONSTANTS;
  partner: PartnerDbModel;
  subscriptionDetail: SubscriptionModel;
  isLexOfficeEnabled = false;
  
  onlinePaymentSettingRoutes = [
    '/app/settings/lexoffice'
  ];
  worker: WorkerDbModel;

  constructor(
    private onlinePaymentService: OnlinePaymentService,
    private auth0Service: Auth0Service,
  ) {
    this.onlinePaymentService.verifyBexioAccountEvent.subscribe(
      (result: any) => {
        this.verifyOnlinePaymentStatus();
      }
    );
  }

  verifyOnlinePaymentStatus() {
    if (!this.auth0Service.checkRolesPermission('read:online-payment-settings')) {
      this.isLexOfficeEnabled = false;
      return false;
    }

    this.onlinePaymentService.verifyOnlinePaymentStatus().subscribe({
      next: (verifyPartners: OnlinePaymentModel) => {
        this.isLexOfficeEnabled = verifyPartners.lexOfficeEnabled
      }
    });
  }
}
