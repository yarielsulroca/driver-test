import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExamResultsTable } from './exam-results-table';

describe('ExamResultsTable', () => {
  let component: ExamResultsTable;
  let fixture: ComponentFixture<ExamResultsTable>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ExamResultsTable]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ExamResultsTable);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
