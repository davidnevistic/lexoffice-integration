import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LexofficeSettingsComponent } from './lexoffice-settings.component';

describe('LexofficeSettingsComponent', () => {
  let component: LexofficeSettingsComponent;
  let fixture: ComponentFixture<LexofficeSettingsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ LexofficeSettingsComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(LexofficeSettingsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
